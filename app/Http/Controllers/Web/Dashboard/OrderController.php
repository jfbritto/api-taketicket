<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('manage', $event);

        $orders = $event->orders()
            ->with('user', 'items.ticketType', 'payment')
            ->latest()
            ->paginate(15);

        return view('dashboard.events.orders', compact('event', 'orders'));
    }

    public function show(Request $request, Event $event, Order $order): View
    {
        $this->authorize('manage', $event);
        abort_if($order->event_id !== $event->id, 404);

        $order->load('user', 'items.ticketType', 'payment');
        $tickets = \App\Models\Ticket::whereIn('order_item_id', $order->items->pluck('id'))
            ->with('participant')
            ->get();

        return view('dashboard.events.order-show', compact('event', 'order', 'tickets'));
    }
}
