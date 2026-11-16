<?php

namespace App\Http\Middleware;

use App\Models\Services;
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

        // Une équipe désactivée (ou supprimée) perd l'accès immédiatement, même session ouverte
        $actif = Services::where('id', session('equipe_id'))
            ->where('type_compte', 'equipe')
            ->where('etat_compte', 'actif')
            ->exists();

        if (! $actif) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect()->route('services.login')
                ->with('error', 'Ce compte équipe est désactivé.');
        }

        // Connecté en tant qu'agent de l'équipe : son accès doit rester valide
        if (session()->has('agent_id')) {
            $agent = \App\Models\Agent::find(session('agent_id'));

            if (! $agent || ! $agent->peutSeConnecter()) {
                $request->session()->flush();
                $request->session()->regenerate();

                return redirect()->route('agent.login')->with('error', 'Votre accès est suspendu. Contactez votre service.');
            }

            if ($agent->doit_changer_mdp) {
                return redirect()->route('agent.password');
            }
        }

        return $next($request);
    }
}
