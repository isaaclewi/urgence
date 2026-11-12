<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AlerteAffectation;
use App\Models\AlerteIntervention;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceEquipeController extends Controller
{
    /**
     * Équipe appartenant au service connecté — jamais un autre compte.
     */
    private function equipeDuService($id): Services
    {
        return Services::where('id', $id)
            ->where('parent_service_id', session('service_id'))
            ->where('type_compte', 'equipe')
            ->firstOrFail();
    }

    /** L'équipe a-t-elle un historique (affectations, interventions) qui disparaîtrait avec elle ? */
    private function aUnHistorique(int $equipeId): bool
    {
        return AlerteAffectation::where('service_destination_id', $equipeId)->exists()
            || AlerteIntervention::where('equipe_id', $equipeId)->exists();
    }

    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        $service = Services::findOrFail(session('service_id'));

        // Uniquement les vraies équipes terrain (les autres comptes enfants ne sont pas modifiables ici)
        $equipes = Services::where('parent_service_id', $service->id)
            ->where('type_compte', 'equipe')
            ->orderBy('nom')
            ->get();

        $ids = $equipes->pluck('id');

        $agents = Agent::whereIn('equipe_id', $ids)
            ->selectRaw('equipe_id, COUNT(*) as total')->groupBy('equipe_id')
            ->pluck('total', 'equipe_id');

        $interventions = AlerteIntervention::whereIn('equipe_id', $ids)
            ->selectRaw('equipe_id, COUNT(*) as total')->groupBy('equipe_id')
            ->pluck('total', 'equipe_id');

        return view('services.equipes.index', compact('service', 'equipes', 'agents', 'interventions'));
    }

    public function create()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        $service = Services::find(session('service_id'));

        return view('services.equipes.create', compact('service'));
    }

    public function store(Request $request)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login');
        }

        // L'email sert d'identifiant de connexion : obligatoire et unique.
        $request->validate([
            'nom'       => 'required|string|max:191',
            'adresse'   => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'email'     => ['required', 'email', Rule::unique('services', 'email')],
        ]);

        // Mot de passe temporaire aléatoire, affiché une seule fois.
        $motDePasse = Str::random(10);

        Services::create([
            'nom'               => $request->nom,
            'adresse'           => $request->adresse,
            'telephone'         => $request->telephone,
            'email'             => $request->email,
            'role'              => 'equipe',
            'type_compte'       => 'equipe',
            'parent_service_id' => session('service_id'),
            'disponible'        => true,
            'etat_compte'       => 'actif',
            'password'          => Hash::make($motDePasse),
        ]);

        return redirect()
            ->route('services.equipes.index')
            ->with('success', "Équipe créée. Mot de passe temporaire : {$motDePasse} (à communiquer à l'équipe, il ne sera plus affiché).");
    }

    public function edit($id)
    {
        $equipe = $this->equipeDuService($id);

        return view('services.equipes.edit', compact('equipe'));
    }

    public function update(Request $request, $id)
    {
        $equipe = $this->equipeDuService($id);

        $request->validate([
            'nom'       => 'required|string|max:191',
            'telephone' => 'nullable|string|max:50',
            'adresse'   => 'nullable|string|max:255',
            'email'     => ['required', 'email', Rule::unique('services', 'email')->ignore($equipe->id)],
            'password'  => 'nullable|string|min:8',
        ]);

        $donnees = [
            'nom'       => $request->nom,
            'telephone' => $request->telephone,
            'adresse'   => $request->adresse,
            'email'     => $request->email,
        ];

        // Nouveau mot de passe uniquement s'il est saisi
        if ($request->filled('password')) {
            $donnees['password'] = Hash::make($request->password);
        }

        $equipe->update($donnees);

        return redirect()
            ->route('services.equipes.index')
            ->with('success', $request->filled('password') ? 'Équipe modifiée, mot de passe changé.' : 'Équipe modifiée.');
    }

    /**
     * Désactiver / réactiver une équipe : elle ne peut plus se connecter, son historique est conservé.
     */
    public function statut($id)
    {
        $equipe = $this->equipeDuService($id);

        $actif = $equipe->etat_compte === 'actif';
        $equipe->update(['etat_compte' => $actif ? 'inactif' : 'actif']);

        return back()->with('success', $actif
            ? "Équipe « {$equipe->nom} » désactivée : elle ne peut plus se connecter."
            : "Équipe « {$equipe->nom} » réactivée.");
    }

    /**
     * Suppression : refusée si l'équipe a un historique d'interventions, car la base le supprimerait
     * en cascade (rapports, KPI et exports perdraient ces interventions). On propose la désactivation.
     */
    public function destroy($id)
    {
        $equipe = $this->equipeDuService($id);

        if ($this->aUnHistorique($equipe->id)) {
            return back()->with('error', "« {$equipe->nom} » a un historique d'interventions : la supprimer l'effacerait. Désactivez-la plutôt.");
        }

        $equipe->delete();   // ses agents sont simplement détachés (equipe_id = null)

        return back()->with('success', 'Équipe supprimée.');
    }
}
