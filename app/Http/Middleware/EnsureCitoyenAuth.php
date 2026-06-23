<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCitoyenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('citoyen_id')) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter.');
        }

        return $next($request);
    }
}
