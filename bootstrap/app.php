<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Railway / reverse proxies terminate TLS; trust forwarded headers.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SetTenantFromAuthenticatedUser::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SetTenantFromAuthenticatedUser::class,
        ]);
        $middleware->alias([
            'log.access' => \App\Http\Middleware\LogAccessMiddleware::class,
            'api.permission' => \App\Http\Middleware\ApiPermissionMiddleware::class,
            'super.admin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
