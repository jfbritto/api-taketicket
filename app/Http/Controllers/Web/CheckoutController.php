<?php

namespace App\Http\Controllers\Web;

use App\DTO\CreateOrderDTO;
use App\Enums\BillingType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function createOrder(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        // Build items with placeholder participants (will be filled in checkout form)
        $items = [];
        foreach ($validated['items'] as $item) {
            if ($item['quantity'] < 1) continue;
            $participants = [];
            for ($i = 0; $i < $item['quantity']; $i++) {
                $participants[] = ['name' => 'Pending', 'email' => 'pending@pending.com'];
            }
            $items[] = [
                'ticket_type_id' => $item['ticket_type_id'],
                'quantity' => $item['quantity'],
                'participants' => $participants,
            ];
        }

        if (empty($items)) {
            return back()->with('error', 'Please select at least one ticket.');
        }

        try {
            $dto = new CreateOrderDTO(
                eventId: $event->id,
                billingType: BillingType::PIX, // placeholder, real billing type selected at payment
                items: $items,
            );
            $order = $orderService->createOrder($request->user(), $dto);
            return redirect()->route('checkout.show', $order);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $order->load('event.customFields', 'items.ticketType');
        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('checkout.show', compact('order', 'tickets'));
    }

    public function saveParticipants(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $validated = $request->validate([
            'participants' => 'required|array',
            'participants.*.ticket_id' => 'required|exists:tickets,id',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.email' => 'required|email',
            'participants.*.phone' => 'nullable|string',
            'participants.*.document' => 'nullable|string',
            'participants.*.custom_fields' => 'nullable|array',
        ]);

        foreach ($validated['participants'] as $data) {
            $ticket = Ticket::findOrFail($data['ticket_id']);
            if ($ticket->participant) {
                $ticket->participant->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'document' => $data['document'] ?? null,
                ]);
            }
            if (!empty($data['custom_fields'])) {
                foreach ($data['custom_fields'] as $fieldId => $value) {
                    \App\Models\ParticipantFieldValue::updateOrCreate(
                        ['participant_id' => $ticket->participant->id, 'custom_field_id' => $fieldId],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->route('checkout.payment', $order);
    }

    public function payment(Request $request, Order $order): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        if ($order->status === OrderStatus::PAID) {
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        $order->load('event', 'items.ticketType', 'payment');

        return view('checkout.payment', compact('order'));
    }

    public function processPayment(Request $request, Order $order, PaymentService $paymentService): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->isExpired()) {
            return redirect()->route('event.show', $order->event->slug)
                ->with('error', 'Your payment session expired. Please select your tickets again.');
        }

        $validated = $request->validate([
            'billing_type' => 'required|in:PIX,CREDIT_CARD',
        ]);

        $billingType = BillingType::from($validated['billing_type']);

        $payment = $order->payment;
        if (!$payment) {
            $payment = $paymentService->createPayment($order, $billingType);
            $order->update(['status' => OrderStatus::AWAITING_PAYMENT]);
        }

        if ($billingType === BillingType::PIX) {
            $pixData = $paymentService->getPixQrCode($payment);
            $order->load('event', 'items.ticketType');
            return view('checkout.payment', compact('order', 'pixData'));
        }

        // Credit Card: redirect to Asaas hosted payment page
        if ($payment->invoice_url) {
            return redirect()->away($payment->invoice_url);
        }

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return response()->json([
            'status' => $order->fresh()->status->value,
        ]);
    }

    public function success(Request $request): View
    {
        $order = Order::where('id', $request->query('order'))
            ->where('user_id', $request->user()->id)
            ->with('event', 'items.ticketType')
            ->firstOrFail();

        $tickets = Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('checkout.success', compact('order', 'tickets'));
    }

    public function cancel(Request $request): View
    {
        $order = Order::where('id', $request->query('order'))
            ->where('user_id', $request->user()->id)
            ->with('event')
            ->first();

        return view('checkout.cancel', compact('order'));
    }
}
