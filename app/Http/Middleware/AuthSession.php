<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
         if (!session()->has('id')) {
            // Redirection si pas connecté
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        return $next($request);
    }

}
