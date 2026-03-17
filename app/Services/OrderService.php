<?php

namespace App\Services;

use App\Actions\CalculatePlatformFee;
use App\DTO\CreateOrderDTO;
use App\DTO\CreateParticipantDTO;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Exceptions\InsufficientStockException;
use App\Jobs\GenerateTicketsJob;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly CalculatePlatformFee $calculateFee,
        private readonly PaymentService $paymentService,
        private readonly ParticipantService $participantService,
    ) {}

    public function createOrder(User $user, CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($user, $dto) {
            $totalAmount = 0;
            $itemsData = [];

            // Validate and reserve stock
            foreach ($dto->items as $item) {
                $ticketType = TicketType::lockForUpdate()->findOrFail($item['ticket_type_id']);

                if ($ticketType->available < $item['quantity']) {
                    throw new InsufficientStockException($ticketType->name);
                }

                // Check sales window
                abort_if(! $ticketType->isOnSale(), 422, "Ticket type '{$ticketType->name}' is not on sale");

                // Check max per user
                abort_if(
                    $item['quantity'] > $ticketType->max_per_user,
                    422,
                    "Maximum {$ticketType->max_per_user} tickets per purchase for '{$ticketType->name}'"
                );

                $ticketType->decrement('available', $item['quantity']);
                $totalAmount += $ticketType->price * $item['quantity'];

                $itemsData[] = [
                    'ticketType' => $ticketType,
                    'quantity' => $item['quantity'],
                    'participants' => $item['participants'],
                ];
            }

            // Calculate fees
            $fees = $this->calculateFee->execute($totalAmount);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'event_id' => $dto->eventId,
                'status' => OrderStatus::PENDING,
                'total_amount' => $totalAmount,
                'platform_fee' => $fees['platform_fee'],
                'organizer_amount' => $fees['organizer_amount'],
                'expires_at' => now()->addMinutes(15),
            ]);

            // Create order items + tickets + participants
            foreach ($itemsData as $itemData) {
                $orderItem = $order->items()->create([
                    'ticket_type_id' => $itemData['ticketType']->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['ticketType']->price,
                ]);

                // Create tickets and participants
                foreach ($itemData['participants'] as $participantData) {
                    $ticket = Ticket::create([
                        'event_id' => $order->event_id,
                        'ticket_type_id' => $itemData['ticketType']->id,
                        'order_item_id' => $orderItem->id,
                        'ticket_code' => 'PENDING-'.Str::uuid(), // Generated after payment
                        'qr_code_payload' => '', // Generated after payment
                        'status' => TicketStatus::VALID,
                    ]);

                    $participantDto = CreateParticipantDTO::fromArray($participantData);
                    $this->participantService->createParticipant($ticket, $participantDto);
                }
            }

            // Handle payment
            if ($totalAmount > 0) {
                $this->paymentService->createPayment($order, $dto->billingType);
                $order->update(['status' => OrderStatus::AWAITING_PAYMENT]);
            } else {
                // Free order — skip payment, go directly to paid
                $order->update(['status' => OrderStatus::PAID]);
                GenerateTicketsJob::dispatch($order);
            }

            return $order->fresh()->load('items', 'payment');
        });
    }

    public function expireOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::EXPIRED]);

            // Restore stock
            foreach ($order->items as $item) {
                $item->ticketType->increment('available', $item->quantity);
            }

            // Cancel tickets
            Ticket::whereIn('order_item_id', $order->items->pluck('id'))
                ->update(['status' => TicketStatus::CANCELLED]);
        });
    }
}
