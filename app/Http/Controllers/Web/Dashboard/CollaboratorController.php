<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\CollaboratorAddedMail;
use App\Mail\CollaboratorInvitedMail;
use App\Models\Event;
use App\Models\EventCollaborator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CollaboratorController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $expiresAt = $event->end_date ?? $event->start_date->addHours(24);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($event, $expiresAt) {
                    if ($expiresAt < now()) {
                        $fail('Não é possível convidar colaboradores para um evento que já encerrou.');
                        return;
                    }

                    if (strtolower($value) === strtolower($event->organizer->user->email)) {
                        $fail('O organizador não pode ser convidado como colaborador.');
                        return;
                    }

                    $exists = EventCollaborator::where('event_id', $event->id)
                        ->whereRaw('LOWER(invitee_email) = ?', [strtolower($value)])
                        ->whereIn('status', ['pending', 'active'])
                        ->exists();

                    if ($exists) {
                        $fail('Este e-mail já foi convidado para este evento.');
                    }
                },
            ],
        ]);

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            EventCollaborator::create([
                'event_id' => $event->id,
                'inviter_user_id' => $request->user()->id,
                'invitee_email' => $validated['email'],
                'user_id' => $existingUser->id,
                'status' => 'active',
                'accepted_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            Mail::to($existingUser->email)->queue(new CollaboratorAddedMail($event));
        } else {
            $collaborator = EventCollaborator::create([
                'event_id' => $event->id,
                'inviter_user_id' => $request->user()->id,
                'invitee_email' => $validated['email'],
                'status' => 'pending',
                'expires_at' => $expiresAt,
            ]);

            $signedUrl = URL::temporarySignedRoute(
                'invitation.accept',
                now()->addDays(7),
                ['collaborator' => $collaborator->id]
            );

            Mail::to($validated['email'])->queue(new CollaboratorInvitedMail($collaborator, $signedUrl));
        }

        return back()->with('success', 'Colaborador adicionado com sucesso.');
    }

    public function destroy(Request $request, Event $event, EventCollaborator $collaborator): RedirectResponse
    {
        $this->authorize('manage', $event);

        if ($collaborator->event_id !== $event->id) {
            abort(403);
        }

        $name = $collaborator->user?->name ?? $collaborator->invitee_email;
        $collaborator->update(['status' => 'revoked']);

        return back()->with('success', "Acesso de [{$name}] revogado.");
    }
}
