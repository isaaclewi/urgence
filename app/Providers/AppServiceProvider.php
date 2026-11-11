<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // HTTPS forcé en ligne (derrière le proxy de l'hébergeur), mais PAS en local :
        // `php artisan serve` ne parle qu'en HTTP, et des liens https://127.0.0.1
        // donnent ERR_CONNECTION_CLOSED. On décide d'après l'hôte, pas d'après .env
        // (env() ne fonctionne plus hors des fichiers config une fois config:cache lancé).
        $hote = $this->app->runningInConsole()
            ? parse_url((string) config('app.url'), PHP_URL_HOST)
            : request()->getHost();

        $hote = strtolower((string) $hote);

        $estLocal = in_array($hote, ['localhost', '127.0.0.1', '::1', '[::1]', ''], true)
            || str_ends_with($hote, '.test')
            || str_ends_with($hote, '.local')
            || str_ends_with($hote, '.localhost');

        if (! $estLocal) {
            URL::forceScheme('https');
        }
    }
}
