<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\EventCollaborator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->organizer) {
            return redirect('/dashboard');
        }

        $collaborations = EventCollaborator::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('event')
            ->get();

        return view('staff.index', compact('collaborations'));
    }
}
