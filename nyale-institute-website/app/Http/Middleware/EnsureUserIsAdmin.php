<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    // Editors can manage content but not the team/board list or user accounts.
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isAdmin()) {
            return redirect()
                ->route('dashboard.index')
                ->with('error', 'Only administrators can access that section.');
        }

        return $next($request);
    }
}
