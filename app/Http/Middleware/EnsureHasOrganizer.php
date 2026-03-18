<?php

namespace App\Http\Middleware;

use App\Models\EventCollaborator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->organizer) {
            $hasActiveCollaboration = EventCollaborator::where('user_id', $request->user()->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->exists();

            return $hasActiveCollaboration
                ? redirect('/staff')
                : redirect('/dashboard/onboarding');
        }

        return $next($request);
    }
}
