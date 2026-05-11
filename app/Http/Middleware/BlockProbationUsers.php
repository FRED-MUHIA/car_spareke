<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockProbationUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user?->isOnProbation() || $user->role === 'admin' || $request->routeIs('logout')) {
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Your account is under probation. Please contact support for help.']);
    }
}
