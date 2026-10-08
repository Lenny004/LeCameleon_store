<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isStaff()) {
            return $next($request);
        }

        if ($user->two_factor_confirmed_at) {
            if (! $request->session()->get('two_factor_passed')) {
                return redirect()->route('two-factor.challenge');
            }

            return $next($request);
        }

        if (config('security.admin_2fa_required')) {
            if ($request->routeIs('admin.two-factor.setup', 'admin.two-factor.confirm')) {
                return $next($request);
            }

            return redirect()->route('admin.two-factor.setup');
        }

        return $next($request);
    }
}
