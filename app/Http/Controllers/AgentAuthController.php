<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('agents.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'matricule' => 'required|string|max:50',
            'password'  => 'required|string',
        ]);

        $matricule = strtoupper(trim($request->matricule));
        $message   = 'Matricule ou mot de passe incorrect.';

        $agent = Agent::whereRaw('UPPER(matricule) = ?', [$matricule])
            ->whereNotNull('password')
            ->get()
            ->first(fn (Agent $a) => Hash::check($request->password, $a->password));

        if (! $agent) {
            return back()->withErrors(['matricule' => $message])->withInput($request->only('matricule'));
        }

        $service = Services::find($agent->service_id);

        if (! $agent->peutSeConnecter() || ! $service || $service->etat_compte !== 'actif') {
            return back()->withErrors(['matricule' => 'Votre accès est suspendu. Contactez votre service.'])
                ->withInput($request->only('matricule'));
        }

        $agent->forceFill(['derniere_connexion' => now()])->save();

        $request->session()->flush();
        session([
            'agent_id'         => $agent->id,
            'agent_nom'        => $agent->nom_complet,
            'agent_matricule'  => $agent->matricule,
            'agent_service_id' => $agent->service_id,
            'compte_type'      => 'agent',
        ]);

        if ($agent->doit_changer_mdp) {
            return redirect()->route('agent.password')->with('success', 'Bienvenue. Choisissez votre mot de passe personnel.');
        }

        return $this->redirigerSelonRole($agent);
    }

    /** Équipe terrain active → interface équipe ; sinon espace personnel de l'agent. */
    public static function redirigerSelonRole(Agent $agent)
    {
        $equipe = $agent->equipe_id
            ? Services::where('id', $agent->equipe_id)->where('type_compte', 'equipe')->where('etat_compte', 'actif')->first()
            : null;

        if ($equipe) {
            session([
                'equipe_id'        => $equipe->id,
                'equipe_nom'       => $equipe->nom,
                'equipe_email'     => $equipe->email,
                'equipe_telephone' => $equipe->telephone,
                'equipe_adresse'   => $equipe->adresse,
                'equipe_photo'     => $equipe->photo_profil,
                'equipe_parent_id' => $equipe->parent_service_id,
            ]);

            return redirect()->route('equipe.dashboard');
        }

        return redirect()->route('agent.espace');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('agent.login')->with('success', 'Déconnecté avec succès.');
    }
}
