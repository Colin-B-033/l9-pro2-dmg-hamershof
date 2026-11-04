<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Alleen gebruikers met is_admin = true mogen verder.
     * Gebruik altijd samen met de 'auth'-middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            abort(403, 'Je hebt geen toegang tot het beheer.');
        }

        return $next($request);
    }
}
