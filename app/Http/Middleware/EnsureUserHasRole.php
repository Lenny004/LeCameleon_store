<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict routes to users with one of the given roles.
 */
class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Unauthorized.');
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role) => explode('|', $role))
            ->flatMap(fn (string $role) => explode(',', $role))
            ->map(fn (string $role) => trim($role))
            ->filter()
            ->values();

        foreach ($allowed as $role) {
            if ($this->userHasRole($user, $role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized.');
    }

    private function userHasRole($user, string $role): bool
    {
        return match ($role) {
            UserRole::Admin->value => $user->isAdmin(),
            UserRole::Staff->value => $user->isStaff(),
            UserRole::Customer->value => $user->role === UserRole::Customer,
            default => $user->role->value === $role,
        };
    }
}
