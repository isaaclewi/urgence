<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Garde;
use App\Models\Services;
use Illuminate\Http\Request;
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
            'matricule'     => ['required', 'string', 'max:50',
                                Rule::unique('agents', 'matricule')->where('service_id', $sid)->ignore($agent?->id)],
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
        $data = $request->validate($this->rules());
        $data['service_id'] = $this->serviceId();

        Agent::create($data);

        return redirect()->route('services.personnel.index')->with('success', 'Agent ajouté avec succès.');
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
