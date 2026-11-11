<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\alertes;
use App\Models\Services;
use App\Models\AlerteAffectation;

class UrgenceController extends Controller
{
    /** Durée maximale d'une connexion SSE (en dessous du timeout nginx par défaut). */
    private const SSE_WINDOW   = 55;
    /** Pause entre deux vérifications en base. */
    private const SSE_INTERVAL = 3;
    /** Intervalle entre deux heartbeats (commentaires SSE). */
    private const SSE_HEARTBEAT = 15;

    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service     = Services::findOrFail(session('service_id'));
        $rolesConnus = $this->rolesConnus();

        $alertes = $this->alertesPourService($service, $rolesConnus)
            ->with('citoyen')
            ->orderByDesc('created_at')
            ->get();

        return view('services.urgenceSignalee', compact('service', 'alertes'));
    }

    // ─────────────────────────────────────────────────
    //  SSE — Nouvelles alertes pour le service connecté
    //  GET /services/urgences/stream
    // ─────────────────────────────────────────────────
    public function stream(Request $request)
    {
        // On lit tout ce qui dépend de la session AVANT d'ouvrir le flux.
        $service = Services::find(session('service_id'));
        abort_if(!$service, 403);

        $rolesConnus = $this->rolesConnus();
        $lastId      = (int) $request->input('lastId', 0);

        return $this->sseResponse(function () use ($service, $rolesConnus, &$lastId) {

            $nouvelles = $this->alertesPourService($service, $rolesConnus)
                ->with('citoyen')
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->get();

            foreach ($nouvelles as $alerte) {
                $payload = [
                    'id'           => $alerte->id,
                    'titre'        => $alerte->titre,
                    'type_alerte'  => $alerte->type_alerte,
                    'localisation' => $alerte->localisation,
                    'statut'       => $alerte->statut,
                    'latitude'     => $alerte->latitude,
                    'longitude'    => $alerte->longitude,
                    'created_at'   => $alerte->created_at->format('d/m/Y H:i'),
                    'citoyen'      => $alerte->citoyen ? [
                        'nom'       => $alerte->citoyen->nom,
                        'prenom'    => $alerte->citoyen->prenom,
                        'email'     => $alerte->citoyen->email,
                        'telephone' => $alerte->citoyen->telephone,
                        'adresse'   => $alerte->citoyen->adresse ?? 'Adresse non renseignée',
                        'photo'     => asset($alerte->citoyen->photo_profil ?? 'medias/default.jpg'),
                    ] : null,
                    'media_photo'  => $alerte->media_photo ? asset($alerte->media_photo) : null,
                    'media_vocal'  => $alerte->media_vocal ? asset($alerte->media_vocal) : null,
                ];

                echo 'id: ' . $alerte->id . "\n";
                echo 'data: ' . json_encode($payload) . "\n\n";
                $lastId = $alerte->id;
            }

            return $nouvelles->isNotEmpty();
        });
    }

    // ─────────────────────────────────────────────────
    //  SSE — Nouvelles affectations pour une équipe
    //  GET /equipe/affectations/stream
    // ─────────────────────────────────────────────────
    public function streamAffectations(Request $request)
    {
        $equipeId = session('equipe_id');
        abort_if(!$equipeId, 403);

        $lastId = (int) $request->input('lastId', 0);

        return $this->sseResponse(function () use ($equipeId, &$lastId) {

            $nouvelles = AlerteAffectation::with(['alerte.citoyen', 'serviceSource'])
                ->where('service_destination_id', $equipeId)
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->get();

            foreach ($nouvelles as $aff) {
                $alerte  = $aff->alerte;
                $payload = [
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

                echo 'id: ' . $aff->id . "\n";
                echo 'data: ' . json_encode($payload) . "\n\n";
                $lastId = $aff->id;
            }

            return $nouvelles->isNotEmpty();
        });
    }

    public function updateStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:en attente,pris en charge,terminee',
        ]);

        $alerte = alertes::findOrFail($id);
        $alerte->statut = $request->statut;
        $alerte->save();

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    public function affecter(Request $request, $id)
    {
        $request->validate([
            'service_destination_id' => 'required|exists:services,id',
            'commentaire'            => 'nullable|string',
        ]);

        $alerte = alertes::findOrFail($id);

        AlerteAffectation::create([
            'alerte_id'              => $alerte->id,
            'service_source_id'      => session('service_id'),
            'service_destination_id' => $request->service_destination_id,
            'affecte_par'            => session('service_id'),
            'commentaire'            => $request->commentaire,
            'statut'                 => 'transmise',
        ]);

        $alerte->equipe_id           = $request->service_destination_id;
        $alerte->statut_intervention = 'affectee';
        $alerte->save();

        return back()->with('success', 'Alerte affectée avec succès.');
    }

    public function interventionsJournalieres()
    {
        // Un service ne voit que les interventions de SES équipes.
        $interventions = DB::table('alerte_interventions')
            ->join('alertes',  'alertes.id',  '=', 'alerte_interventions.alerte_id')
            ->join('services', 'services.id', '=', 'alerte_interventions.equipe_id')
            ->where('services.parent_service_id', session('service_id'))
            ->select(
                'alerte_interventions.*',
                'alertes.titre',
                'alertes.localisation',
                'services.nom as equipe_nom'
            )
            ->orderByDesc('alerte_interventions.created_at')
            ->get();

        return view('services.interventions_journalieres', compact('interventions'));
    }

    // ═════════════════════════════════════════════════
    //  Helpers privés
    // ═════════════════════════════════════════════════

    /** Rôles de services existants (hôpital, police, pompier…). */
    private function rolesConnus(): array
    {
        return Services::whereNotNull('role')->pluck('role')->toArray();
    }

    /**
     * Alertes visibles par un service : celles de son type, plus celles
     * sans type ou dont le type ne correspond à aucun service connu.
     */
    private function alertesPourService(Services $service, array $rolesConnus)
    {
        return alertes::query()->where(function ($q) use ($service, $rolesConnus) {
            $q->where('type_alerte', $service->role)
              ->orWhereNull('type_alerte')
              ->orWhere('type_alerte', '')
              ->orWhereNotIn('type_alerte', $rolesConnus);
        });
    }

    /**
     * Boucle SSE commune. $tick() écrit les nouveaux événements et retourne
     * true s'il en a émis. Le flux s'arrête dès que le client se déconnecte,
     * ce qui libère immédiatement le worker PHP.
     */
    private function sseResponse(callable $tick)
    {
        return response()->stream(function () use ($tick) {

            @set_time_limit(self::SSE_WINDOW + 15);
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $deadline      = time() + self::SSE_WINDOW;
            $lastHeartbeat = 0;

            while (time() < $deadline) {

                if (connection_aborted()) {
                    break;
                }

                if ($tick()) {
                    flush();
                }

                if (time() - $lastHeartbeat >= self::SSE_HEARTBEAT) {
                    echo ": heartbeat\n\n";
                    flush();
                    $lastHeartbeat = time();
                }

                sleep(self::SSE_INTERVAL);
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
