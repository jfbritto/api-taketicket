<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->organizer) {
            return redirect('/dashboard/onboarding');
        }

        return $next($request);
    }
}
