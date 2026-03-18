<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EventCollaborator;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function show(Request $request, EventCollaborator $collaborator): mixed
    {
        if ($collaborator->expires_at < now()) {
            return response()->view('invitations.expired');
        }

        if ($collaborator->status === 'revoked') {
            return response()->view('invitations.revoked');
        }

        if ($collaborator->status === 'active') {
            if (auth()->check()) {
                if (strtolower(auth()->user()->email) !== strtolower($collaborator->invitee_email)) {
                    return response()->view('invitations.wrong-account');
                }
                return redirect()->route('staff.checkin', $collaborator->event);
            }

            session(['url.intended' => route('staff.checkin', $collaborator->event)]);
            return redirect('/login');
        }

        // status === 'pending'
        if (auth()->check()) {
            return response()->view('invitations.already-logged-in');
        }

        session([
            'pending_collaborator_id' => $collaborator->id,
            'pending_collaborator_email' => $collaborator->invitee_email,
        ]);

        return redirect('/register');
    }
}
