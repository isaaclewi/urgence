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
            'auth.citoyen' => \App\Http\Middleware\AuthCitoyen::class,
            'auth.admin'   => \App\Http\Middleware\AuthAdmin::class,
            'auth.service' => \App\Http\Middleware\AuthService::class,
            'auth.equipe'  => \App\Http\Middleware\AuthEquipe::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
