<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.citoyen' => \App\Http\Middleware\EnsureCitoyenAuth::class,
            'auth.admin'   => \App\Http\Middleware\EnsureAdminAuth::class,
            'auth.service' => \App\Http\Middleware\EnsureServiceAuth::class,
            'auth.equipe'  => \App\Http\Middleware\EnsureEquipeAuth::class,
            'no.cache'     => \App\Http\Middleware\NoCache::class, // Ajout du middleware NoCache
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
