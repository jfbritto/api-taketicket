<?php

namespace App\Http\Middleware;

use App\Models\EventCollaborator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsEventCollaborator
{
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event'); // already resolved to Event model via route model binding

        $collaborator = EventCollaborator::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if (! $collaborator || in_array($collaborator->status, ['revoked', 'pending'])) {
            abort(403, 'Você não tem acesso a este evento.');
        }

        if ($collaborator->expires_at !== null && $collaborator->expires_at < now()) {
            abort(403, 'Seu acesso a este evento expirou.');
        }

        return $next($request);
    }
}
