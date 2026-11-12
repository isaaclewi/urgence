<?php

namespace App\Http\Controllers;

use App\Models\AlerteAffectation;
use App\Models\AlerteIntervention;
use App\Models\Services;
use App\Models\alertes;
use App\Support\AlertesScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class UrgenceController extends Controller
{
    private const STATUTS = ['en attente', 'pris en charge', 'terminee'];

    private function serviceConnecte(): Services
    {
        return Services::findOrFail(session('service_id'));
    }

    /** Alerte visible par ce service — sinon 404 (jamais l'alerte d'un autre type de service). */
    private function alerteDuService(Services $service, $id): alertes
    {
        return AlertesScope::pourService($service)->where('alertes.id', $id)->firstOrFail();
    }

    /** Équipes actives rattachées à ce service. */
    private function equipesActives(Services $service)
    {
        return Services::where('parent_service_id', $service->id)
            ->where('etat_compte', 'actif')->orderBy('nom')->get(['id', 'nom']);
    }

    private function stats(Services $service): array
    {
        $n = AlertesScope::pourService($service)
            ->selectRaw('statut, COUNT(*) as n')->groupBy('statut')->pluck('n', 'statut');

        return [
            'attente'  => (int) ($n['en attente'] ?? 0),
            'encours'  => (int) ($n['pris en charge'] ?? 0),
            'terminee' => (int) ($n['terminee'] ?? 0),
            'total'    => (int) $n->sum(),
        ];
    }

    // ─────────────────────────────────────────────────
    //  Page Urgences (filtres + pagination, plus de chargement de toutes les alertes)
    // ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $service = $this->serviceConnecte();

        $liste = AlertesScope::pourService($service)->with(['citoyen', 'equipe:id,nom']);

        if ($q = mb_strtolower(trim((string) $request->query('q')))) {
            $liste->where(fn ($w) => $w
                ->whereRaw('LOWER(alertes.titre) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(alertes.localisation) LIKE ?', ["%{$q}%"])
                ->orWhereRaw('LOWER(alertes.description) LIKE ?', ["%{$q}%"]));
        }

        if (in_array($request->query('statut'), self::STATUTS, true)) {
            $liste->where('alertes.statut', $request->query('statut'));
        }

        $alertes = $liste->orderByDesc('alertes.id')->paginate(20)->withQueryString();

        return view('services.urgenceSignalee', [
            'service' => $service,
            'alertes' => $alertes,
            'stats'   => $this->stats($service),
            'equipes' => $this->equipesActives($service),
            'lastId'  => (int) AlertesScope::pourService($service)->max('alertes.id'),
            'live'    => $alertes->onFirstPage() && ! $request->hasAny(['q', 'statut']),
        ]);
    }

    // ─────────────────────────────────────────────────
    //  Polling JSON — nouvelles alertes du service connecté
    //  GET /services/urgences/stream?lastId=123
    //  Renvoie les lignes déjà rendues (même partial que la page) et les compteurs à jour.
    // ─────────────────────────────────────────────────
    public function stream(Request $request)
    {
        $service = Services::find(session('service_id'));
        abort_if(! $service, 403);

        $lastId = (int) $request->input('lastId', 0);

        $nouvelles = AlertesScope::pourService($service)
            ->with(['citoyen', 'equipe:id,nom'])
            ->where('alertes.id', '>', $lastId)
            ->orderBy('alertes.id')->limit(50)->get();

        return response()->json([
            'items' => $nouvelles->map(fn ($a) => [
                'id' => $a->id, 'titre' => $a->titre, 'localisation' => $a->localisation,
                'type_alerte' => $a->type_alerte, 'statut' => $a->statut,
                'html' => view('services.partials.urgence-row', ['alerte' => $a])->render(),
            ])->values(),
            'stats' => $this->stats($service),
        ])->header('Cache-Control', 'no-store');
    }

    // ─────────────────────────────────────────────────
    //  Polling JSON — Nouvelles affectations pour une équipe
    //  GET /equipe/affectations/stream?lastId=123
    // ─────────────────────────────────────────────────
    public function streamAffectations(Request $request)
    {
        $equipeId = session('equipe_id');
        abort_if(!$equipeId, 403);

        $lastId = (int) $request->input('lastId', 0);

        $items = AlerteAffectation::with(['alerte.citoyen', 'serviceSource'])
            ->where('service_destination_id', $equipeId)
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn ($aff) => $this->payloadAffectation($aff))
            ->values();

        return response()->json(['items' => $items])
            ->header('Cache-Control', 'no-store');
    }

    // ─────────────────────────────────────────────────
    //  Formulaire public d'urgence (page d'accueil)
    //  POST /alerte/enregistrer
    //  (la route pointait ici alors que la méthode n'existait pas → erreur 500)
    // ─────────────────────────────────────────────────
    public function enregistrerAlerte(Request $request)
    {
        // Formulaire ouvert à tous : on limite le spam (5 alertes / 10 min par adresse IP).
        $cle = 'alerte-publique|' . $request->ip();

        if (RateLimiter::tooManyAttempts($cle, 5)) {
            $secondes = RateLimiter::availableIn($cle);
            $message  = "Trop d'alertes envoyées depuis cet appareil. Réessayez dans " . ceil($secondes / 60) . ' minute(s).';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 429)
                : back()->withInput()->with('error', $message);
        }

        $request->validate([
            'type_alerte'  => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'audio'        => 'nullable|file|mimes:webm,mp3,wav,ogg,m4a,mp4|max:10240',
        ]);

        RateLimiter::hit($cle, 600);

        // Message vocal : un échec de stockage ne doit jamais empêcher l'envoi de l'alerte.
        $vocal = null;
        if ($request->hasFile('audio')) {
            try {
                $fichier = $request->file('audio');
                $nom     = 'vocal_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $fichier->extension();

                Storage::disk('supabase')->putFileAs('alertes/vocaux', $fichier, $nom);
                $vocal = config('filesystems.disks.supabase.public_url') . '/alertes/vocaux/' . $nom;
            } catch (\Throwable $e) {
                Log::warning('Alerte : échec du stockage du message vocal', ['erreur' => $e->getMessage()]);
            }
        }

        alertes::create([
            'titre'        => $request->type_alerte,
            'description'  => $request->description,
            'date_heure'   => now(),
            'localisation' => $request->localisation,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'media_vocal'  => $vocal,
            'statut'       => 'en attente',
            'type_alerte'  => $request->type_alerte,
            'citoyen_id'   => session('citoyen_id'),   // null si la personne n'est pas connectée
            'services_id'  => null,
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Alerte envoyée avec succès !'])
            : back()->with('success', 'Alerte envoyée avec succès !');
    }

    // ─────────────────────────────────────────────────
    //  Changer le statut d'une urgence
    // ─────────────────────────────────────────────────
    public function updateStatut(Request $request, $id)
    {
        $service = $this->serviceConnecte();
        $alerte  = $this->alerteDuService($service, $id);

        $request->validate(['statut' => 'required|in:' . implode(',', self::STATUTS)]);

        if ($alerte->statut === 'terminee' && $request->statut !== 'terminee') {
            return back()->with('error', 'Une urgence terminée ne peut pas être rouverte.');
        }

        DB::transaction(function () use ($alerte, $request) {
            $alerte->statut = $request->statut;

            if ($request->statut === 'terminee') {
                $alerte->statut_intervention = 'terminee';

                // On clôture aussi ce qui était encore ouvert côté équipes
                AlerteAffectation::where('alerte_id', $alerte->id)
                    ->whereIn('statut', ['transmise', 'en_cours'])->update(['statut' => 'terminee']);
                AlerteIntervention::where('alerte_id', $alerte->id)
                    ->where('statut', '!=', 'terminee')->update(['statut' => 'terminee']);
            }

            $alerte->save();
        });

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    // ─────────────────────────────────────────────────
    //  Affecter (ou réaffecter) une équipe
    // ─────────────────────────────────────────────────
    public function affecter(Request $request, $id)
    {
        $service = $this->serviceConnecte();
        $alerte  = $this->alerteDuService($service, $id);

        $request->validate([
            'service_destination_id' => 'required|integer',
            'commentaire'            => 'nullable|string|max:1000',
        ]);

        if ($alerte->statut === 'terminee') {
            return back()->with('error', 'Cette urgence est terminée : elle ne peut plus être affectée.');
        }

        // L'équipe doit appartenir à CE service et être active
        $equipe = Services::where('id', $request->service_destination_id)
            ->where('parent_service_id', $service->id)
            ->where('etat_compte', 'actif')->first();

        if (! $equipe) {
            return back()->with('error', 'Équipe introuvable ou désactivée.');
        }

        $ouvertes = AlerteAffectation::with('serviceDestination:id,nom')
            ->where('alerte_id', $alerte->id)->whereIn('statut', ['transmise', 'en_cours'])->get();

        if ($enCours = $ouvertes->firstWhere('statut', 'en_cours')) {
            return back()->with('error', "L'équipe « " . ($enCours->serviceDestination->nom ?? '?') . " » est déjà en intervention sur cette urgence.");
        }

        if ($ouvertes->contains('service_destination_id', $equipe->id)) {
            return back()->with('error', "Cette urgence est déjà affectée à « {$equipe->nom} ».");
        }

        DB::transaction(function () use ($alerte, $ouvertes, $equipe, $service, $request) {
            // Réaffectation : l'ancienne affectation non démarrée est annulée (et non supprimée : les délais restent exacts)
            foreach ($ouvertes as $ancienne) {
                $ancienne->update(['statut' => 'annulee']);
            }

            AlerteAffectation::create([
                'alerte_id'              => $alerte->id,
                'service_source_id'      => $service->id,
                'service_destination_id' => $equipe->id,
                'affecte_par'            => $service->id,
                'commentaire'            => $request->commentaire,
                'statut'                 => 'transmise',
            ]);

            $alerte->equipe_id           = $equipe->id;
            $alerte->statut_intervention = 'affectee';
            $alerte->save();
        });

        return back()->with('success', "Urgence affectée à « {$equipe->nom} ».");
    }

    // ─────────────────────────────────────────────────
    //  Interventions des équipes du service (filtres + pagination)
    // ─────────────────────────────────────────────────
    public function interventionsJournalieres(Request $request)
    {
        $service = $this->serviceConnecte();
        $equipes = Services::where('parent_service_id', $service->id)->orderBy('nom')->get(['id', 'nom']);

        $base = fn () => DB::table('alerte_interventions as i')
            ->join('alertes as a', 'a.id', '=', 'i.alerte_id')
            ->join('services as e', 'e.id', '=', 'i.equipe_id')
            ->where('e.parent_service_id', $service->id);

        $liste = $base();

        if ($request->filled('du')) {
            $liste->whereDate('i.created_at', '>=', $request->query('du'));
        }
        if ($request->filled('au')) {
            $liste->whereDate('i.created_at', '<=', $request->query('au'));
        }
        if ($request->filled('equipe') && $equipes->contains('id', (int) $request->query('equipe'))) {
            $liste->where('i.equipe_id', (int) $request->query('equipe'));
        }
        if (in_array($request->query('statut'), ['en_cours', 'terminee'], true)) {
            $liste->where('i.statut', $request->query('statut'));
        }

        $stats = [
            'total'   => $base()->count(),
            'encours' => $base()->where('i.statut', 'en_cours')->count(),
            'terminee' => $base()->where('i.statut', 'terminee')->count(),
            'equipes' => $base()->distinct()->count('i.equipe_id'),
        ];

        $interventions = $liste
            ->select('i.*', 'a.titre', 'a.localisation', 'e.nom as equipe_nom')
            ->orderByDesc('i.created_at')->paginate(20)->withQueryString();

        return view('services.interventions_journalieres', compact('interventions', 'equipes', 'stats'));
    }

    // ═════════════════════════════════════════════════
    //  Helpers privés
    // ═════════════════════════════════════════════════

    private function payloadAffectation($aff): array
    {
        $alerte = $aff->alerte;
        return [
            'id'          => $aff->id,
            'statut'      => $aff->statut,
            'commentaire' => $aff->commentaire,
            'created_at'  => $aff->created_at->diffForHumans(),
            'alerte'      => $alerte ? [
                'id'           => $alerte->id,
                'titre'        => $alerte->titre,
                'localisation' => $alerte->localisation,
                'latitude'     => $alerte->latitude,
                'longitude'    => $alerte->longitude,
                'type_alerte'  => $alerte->type_alerte,
            ] : null,
            'service_source' => $aff->serviceSource?->nom,
        ];
    }
}
