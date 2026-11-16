<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Garde;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PersonnelController extends Controller
{
    public const STATUTS      = ['actif', 'en congé', 'indisponible', 'inactif'];
    public const TYPES_GARDE  = ['jour', 'nuit', 'astreinte'];

    private function serviceId(): int
    {
        return (int) session('service_id');
    }

    /** Équipes (sous-services) du service connecté. */
    private function equipes()
    {
        return Services::where('parent_service_id', $this->serviceId())
            ->orderBy('nom')
            ->get(['id', 'nom']);
    }

    /** Retrouve un agent appartenant au service connecté, sinon 404. */
    private function agentOuEchec($id): Agent
    {
        return Agent::where('service_id', $this->serviceId())->findOrFail($id);
    }

    private function rules(?Agent $agent = null): array
    {
        $sid = $this->serviceId();

        return [
            'nom'           => ['required', 'string', 'max:100'],
            'prenom'        => ['required', 'string', 'max:100'],
            'fonction'      => ['required', 'string', 'max:100'],
            'grade'         => ['nullable', 'string', 'max:100'],
            'telephone'     => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:150'],
            'statut'        => ['required', Rule::in(self::STATUTS)],
            'equipe_id'     => ['nullable', Rule::exists('services', 'id')->where('parent_service_id', $sid)],
            'competences'   => ['nullable', 'string', 'max:500'],
            'date_embauche' => ['nullable', 'date'],
        ];
    }

    // ───────────────────────── Agents ─────────────────────────

    public function index(Request $request)
    {
        $sid   = $this->serviceId();
        $query = Agent::with('equipe')->where('service_id', $sid);

        if ($request->filled('q')) {
            $like = '%' . mb_strtolower($request->q) . '%';
            $query->where(function ($w) use ($like) {
                $w->whereRaw('LOWER(nom) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(prenom) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(matricule) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(fonction) LIKE ?', [$like]);
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('equipe_id')) {
            $query->where('equipe_id', $request->equipe_id);
        }

        $agents = $query->orderBy('nom')->orderBy('prenom')->paginate(15)->withQueryString();

        $stats = [
            'total'    => Agent::where('service_id', $sid)->count(),
            'actifs'   => Agent::where('service_id', $sid)->where('statut', 'actif')->count(),
            'conges'   => Agent::where('service_id', $sid)->where('statut', 'en congé')->count(),
            'en_garde' => Garde::where('service_id', $sid)
                               ->where('date_debut', '<=', now())
                               ->where('date_fin', '>=', now())
                               ->count(),
        ];

        return view('services.personnel.index', [
            'agents'  => $agents,
            'stats'   => $stats,
            'equipes' => $this->equipes(),
            'statuts' => self::STATUTS,
        ]);
    }

    public function create()
    {
        return view('services.personnel.form', [
            'agent'   => new Agent(['statut' => 'actif']),
            'equipes' => $this->equipes(),
            'statuts' => self::STATUTS,
        ]);
    }

    public function store(Request $request)
    {
        $data    = $request->validate($this->rules());
        $service = Services::findOrFail($this->serviceId());
        $mdp     = Str::random(10);

        for ($essai = 1; ; $essai++) {
            try {
                $agent = Agent::create($data + [
                    'service_id'       => $service->id,
                    'matricule'        => Agent::genererMatricule($service),
                    'password'         => Hash::make($mdp),
                    'compte_actif'     => true,
                    'doit_changer_mdp' => true,
                ]);
                break;
            } catch (QueryException $e) {
                if ($essai >= 3) {
                    throw $e;
                }
            }
        }

        return redirect()->route('services.personnel.index')->with('success', $this->messageAcces($agent, $mdp, 'Agent ajouté.'));
    }

    private function messageAcces(Agent $agent, string $mdp, string $debut): string
    {
        return "{$debut} Connexion sur " . route('agent.login') . " — matricule : {$agent->matricule} — mot de passe temporaire : {$mdp} (affiché une seule fois, à communiquer à l'agent ; il devra le changer à sa première connexion).";
    }

    /** Génère (ou régénère) l'accès de l'agent avec un nouveau mot de passe temporaire. */
    public function resetAcces($id)
    {
        $agent = $this->agentOuEchec($id);
        $mdp   = Str::random(10);

        $agent->update(['password' => Hash::make($mdp), 'doit_changer_mdp' => true, 'compte_actif' => true]);

        return back()->with('success', $this->messageAcces($agent, $mdp, "Accès de {$agent->nom_complet} réinitialisé."));
    }

    /** Suspend ou rétablit la connexion de l'agent (sa fiche est conservée). */
    public function toggleAcces($id)
    {
        $agent = $this->agentOuEchec($id);
        $agent->update(['compte_actif' => ! $agent->compte_actif]);

        return back()->with('success', $agent->compte_actif
            ? "Connexion de {$agent->nom_complet} rétablie."
            : "Connexion de {$agent->nom_complet} suspendue.");
    }

    public function edit($id)
    {
        return view('services.personnel.form', [
            'agent'   => $this->agentOuEchec($id),
            'equipes' => $this->equipes(),
            'statuts' => self::STATUTS,
        ]);
    }

    public function update(Request $request, $id)
    {
        $agent = $this->agentOuEchec($id);
        $agent->update($request->validate($this->rules($agent)));

        return redirect()->route('services.personnel.index')->with('success', 'Agent mis à jour.');
    }

    public function destroy($id)
    {
        $this->agentOuEchec($id)->delete();

        return redirect()->route('services.personnel.index')->with('success', 'Agent supprimé.');
    }

    // ─────────────────── Planning des gardes ───────────────────

    public function gardes()
    {
        $sid = $this->serviceId();

        $gardes = Garde::with('agent')
            ->where('service_id', $sid)
            ->where('date_fin', '>=', now())
            ->orderBy('date_debut')
            ->get();

        $agents = Agent::where('service_id', $sid)
            ->where('statut', 'actif')
            ->orderBy('nom')->orderBy('prenom')
            ->get();

        return view('services.personnel.gardes', [
            'gardes' => $gardes,
            'agents' => $agents,
            'types'  => self::TYPES_GARDE,
        ]);
    }

    public function storeGarde(Request $request)
    {
        $sid = $this->serviceId();

        $data = $request->validate([
            'agent_id'   => ['required', Rule::exists('agents', 'id')->where('service_id', $sid)],
            'type'       => ['required', Rule::in(self::TYPES_GARDE)],
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after:date_debut'],
            'note'       => ['nullable', 'string', 'max:255'],
        ]);

        $chevauche = Garde::where('agent_id', $data['agent_id'])
            ->where('date_debut', '<', $data['date_fin'])
            ->where('date_fin', '>', $data['date_debut'])
            ->exists();

        if ($chevauche) {
            return back()->withInput()->withErrors([
                'agent_id' => 'Cet agent a déjà une garde sur cette période.',
            ]);
        }

        $data['service_id'] = $sid;
        Garde::create($data);

        return redirect()->route('services.personnel.gardes')->with('success', 'Garde planifiée.');
    }

    public function destroyGarde($id)
    {
        Garde::where('service_id', $this->serviceId())->findOrFail($id)->delete();

        return redirect()->route('services.personnel.gardes')->with('success', 'Garde supprimée.');
    }
}
