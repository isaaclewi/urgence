<?php

namespace App\Http\Middleware;

use App\Models\Agent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('agent_id')) {
            return redirect()->route('agent.login')->with('error', 'Veuillez vous connecter.');
        }

        // Un compte suspendu, un agent inactif ou supprimé perd l'accès immédiatement.
        $agent = Agent::find(session('agent_id'));

        if (! $agent || ! $agent->peutSeConnecter()) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect()->route('agent.login')->with('error', 'Votre accès est suspendu. Contactez votre service.');
        }

        $request->attributes->set('agent', $agent);

        return $next($request);
    }
}
