<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEquipeAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('equipe_id')) {
            return redirect()->route('services.login')
                ->with('error', 'Veuillez vous connecter en tant qu\'équipe.');
        }

        return $next($request);
    }
}
