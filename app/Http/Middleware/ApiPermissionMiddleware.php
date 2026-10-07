<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $module, string $action): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasPermission($module, $action)) {
            return response()->json([
                'success' => false,
                'message' => 'Permission insuffisante.',
                'errors' => ['permission' => ['Acces refuse.']],
            ], 403);
        }

        return $next($request);
    }
}
