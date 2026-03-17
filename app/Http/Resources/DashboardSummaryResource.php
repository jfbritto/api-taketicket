<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $event = $this->resource;

        $paidOrders = $event->orders()->where('status', OrderStatus::PAID);

        return [
            'total_orders' => (clone $paidOrders)->count(),
            'total_revenue' => (float) (clone $paidOrders)->sum('total_amount'),
            'organizer_revenue' => (float) (clone $paidOrders)->sum('organizer_amount'),
            'total_tickets' => $event->tickets()->count(),
            'checked_in' => $event->tickets()->where('status', TicketStatus::USED)->count(),
            'tickets_available' => (int) $event->ticketTypes()->sum('available'),
        ];
    }
}
