<?php

namespace App\Http\Controllers;

use App\Models\Garde;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentEspaceController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->attributes->get('agent');

        $gardes = Garde::where('agent_id', $agent->id)
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->get();

        $suivis = \App\Models\InscriptionVaccin::with('programme')
            ->where('agent_id', $agent->id)->whereIn('statut', ['inscrit', 'en_cours'])->get();

        return view('agents.espace', [
            'suivis'  => $suivis,
            'agent'   => $agent,
            'service' => Services::find($agent->service_id),
            'equipe'  => $agent->equipe_id ? Services::find($agent->equipe_id) : null,
            'gardes'  => $gardes,
            'enGarde' => $gardes->first(fn ($g) => $g->estEnCours()),
        ]);
    }

    public function passwordForm()
    {
        return view('agents.password');
    }

    public function passwordUpdate(Request $request)
    {
        $agent = $request->attributes->get('agent');

        $request->validate([
            'password_actuel' => 'required|string',
            'password'        => 'required|string|min:8|confirmed|different:password_actuel',
        ]);

        if (! Hash::check($request->password_actuel, $agent->password)) {
            return back()->withErrors(['password_actuel' => 'Mot de passe actuel incorrect.']);
        }

        $agent->update(['password' => Hash::make($request->password), 'doit_changer_mdp' => false]);

        return AgentAuthController::redirigerSelonRole($agent)->with('success', 'Mot de passe enregistré.');
    }
}
