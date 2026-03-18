<?php

namespace App\Http\Controllers\Web;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $query = Event::where('status', EventStatus::PUBLISHED)
            ->where('start_date', '>=', now())
            ->with('ticketTypes')
            ->orderBy('start_date');

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $events = $query->paginate(12);

        if ($request->boolean('_json')) {
            return response()->json([
                'html'    => view('public._event-cards', compact('events'))->render(),
                'hasMore' => $events->hasMorePages(),
            ]);
        }

        $totalEvents  = Event::where('status', EventStatus::PUBLISHED)->count();
        $totalTickets = Order::where('status', OrderStatus::PAID)->count();

        return view('public.home', compact('events', 'totalEvents', 'totalTickets'));
    }
}
