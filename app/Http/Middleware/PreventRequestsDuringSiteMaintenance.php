<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringSiteMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = SiteSetting::current();

        if (! $settings->maintenance_mode) {
            return $next($request);
        }

        if (Auth::user()?->role === 'admin') {
            return $next($request);
        }

        if ($request->routeIs('login', 'login.store', 'logout', 'mpesa.callback')) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'siteSettings' => $settings,
        ], 503);
    }
}
