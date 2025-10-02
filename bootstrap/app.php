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
        // This is the alias for your Role Middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'subscribed' => \App\Http\Middleware\EnsureUserIsSubscribed::class,
        ]);

        // --- THIS IS THE CRUCIAL FIX ---
        // This adds the necessary middleware to your 'api' group to handle
        // stateful (cookie-based) authentication from your own frontend.
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
   
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
