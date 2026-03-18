<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin', 'editor'], true)) {
            return new JsonResponse([
                'message' => 'This account is not allowed to access the admin panel.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
