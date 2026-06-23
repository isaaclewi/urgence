<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureServiceAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')
                ->with('error', 'Veuillez vous connecter en tant que service.');
        }

        return $next($request);
    }
}
