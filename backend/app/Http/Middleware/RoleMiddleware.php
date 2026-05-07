<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        $role
    ): Response {

        $user = auth()->user();

        if (!$user) {

            abort(401, 'Unauthenticated');
        }

        $userRole = strtolower(
            $user->role->RoleName ?? ''
        );

        if ($userRole !== strtolower($role)) {

            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}