<?php

namespace App\Http\Middleware;

use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantFromAuthenticatedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        CurrentTenant::forget();

        $user = $request->user();
        if ($user && ! $user->isSuperAdmin()) {
            CurrentTenant::set($user->id_entreprise);
        }

        return $next($request);
    }
}
