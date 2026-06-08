<?php

/*
|--------------------------------------------------------------------------
| Définir les variables d'environnement pour InfinityFree
|--------------------------------------------------------------------------
| Comme .env ne fonctionne pas toujours, on définit ici les variables
| directement via putenv(). Laravel va les utiliser normalement.
*/

putenv("APP_ENV=production");
putenv("APP_DEBUG=false");
putenv("APP_URL=https://urgence.ct.ws");

putenv("DB_CONNECTION=mysql");
putenv("DB_HOST=sql209.infinityfree.com");
putenv("DB_PORT=3306");
putenv("DB_DATABASE=if0_40074398_gestion_urgence");
putenv("DB_USERNAME=if0_40074398");
putenv("DB_PASSWORD=gzbPADUGRP");

/*
|--------------------------------------------------------------------------
| Créer l’application Laravel
|--------------------------------------------------------------------------
*/

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
