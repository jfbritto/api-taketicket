<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function index(Request $request): View
    {
        $organizer = $request->user()->organizer;
        $eventIds = $organizer->events()->pluck('id');

        $paidOrders = Order::whereIn('event_id', $eventIds)
            ->where('status', OrderStatus::PAID);

        $totalGross     = (clone $paidOrders)->sum('total_amount');
        $totalFee       = (clone $paidOrders)->sum('platform_fee');
        $totalNet       = (clone $paidOrders)->sum('organizer_amount');
        $totalPaidCount = (clone $paidOrders)->count();

        $recentOrders = (clone $paidOrders)
            ->with(['user', 'event'])
            ->latest('updated_at')
            ->paginate(20);

        return view('dashboard.financeiro', compact(
            'totalGross', 'totalFee', 'totalNet', 'totalPaidCount', 'recentOrders'
        ));
    }
}
