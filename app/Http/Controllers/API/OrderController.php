<?php

namespace App\Http\Controllers\API;

use App\DTO\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService,
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDTO::fromRequest($request->validated());
        $order = $this->orderService->createOrder($request->user(), $dto);

        return response()->json(new OrderResource($order), 201);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with('items.ticketType', 'event', 'payment')
            ->latest()
            ->paginate(15);

        return response()->json(OrderResource::collection($orders)->response()->getData(true));
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items.ticketType', 'payment', 'event');

        $data = (new OrderResource($order))->toArray($request);

        // Include PIX QR code if applicable
        if ($order->payment) {
            $pixData = $this->paymentService->getPixQrCode($order->payment);
            if ($pixData) {
                $data['pix_qr_code'] = $pixData;
            }
        }

        return response()->json($data);
    }
}
