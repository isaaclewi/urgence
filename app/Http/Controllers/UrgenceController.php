<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Services;
use App\Models\AlerteAffectation;

class UrgenceController extends Controller
{
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $serviceId = session('service_id');
        $service   = Services::find($serviceId);

        $rolesConnus = Services::whereNotNull('role')->pluck('role')->toArray();

        $alertes = alertes::with('citoyen')
            ->where(function ($query) use ($service, $rolesConnus) {
                $query->where('type_alerte', $service->role)
                      ->orWhereNull('type_alerte')
                      ->orWhere('type_alerte', '')
                      ->orWhereNotIn('type_alerte', $rolesConnus);
            })
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
        // Désactiver le buffering PHP/Nginx
        if (ob_get_level()) ob_end_clean();

        $response = response()->stream(function () use ($request) {

            $serviceId   = session('service_id');
            $service     = Services::find($serviceId);
            $rolesConnus = Services::whereNotNull('role')->pluck('role')->toArray();

            // L'ID de la dernière alerte déjà connue par le client
            $lastId = (int) $request->input('lastId', 0);

            // Garder la connexion ouverte 55 s (en dessous du timeout nginx par défaut)
            $deadline = time() + 55;

            while (time() < $deadline) {

                // Chercher les alertes plus récentes que $lastId
                $nouvelles = alertes::with('citoyen')
                    ->where('id', '>', $lastId)
                    ->where(function ($q) use ($service, $rolesConnus) {
                        $q->where('type_alerte', $service->role)
                          ->orWhereNull('type_alerte')
                          ->orWhere('type_alerte', '')
                          ->orWhereNotIn('type_alerte', $rolesConnus);
                    })
                    ->orderBy('id')
                    ->get();

                if ($nouvelles->isNotEmpty()) {
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

                        // Format SSE : "data: {json}\n\n"
                        echo "data: " . json_encode($payload) . "\n\n";
                        $lastId = $alerte->id;
                    }

                    // Vider le buffer de sortie immédiatement
                    if (ob_get_level()) ob_flush();
                    flush();
                }

                // Heartbeat toutes les 15 s pour maintenir la connexion
                echo ": heartbeat\n\n";
                if (ob_get_level()) ob_flush();
                flush();

                sleep(3); // Vérifier toutes les 3 secondes
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no', // Désactiver le buffering Nginx
            'Connection'        => 'keep-alive',
        ]);

        return $response;
    }

    // ─────────────────────────────────────────────────
    //  SSE — Nouvelles affectations pour une équipe
    //  GET /equipe/affectations/stream
    // ─────────────────────────────────────────────────
    public function streamAffectations(Request $request)
    {
        if (ob_get_level()) ob_end_clean();

        $response = response()->stream(function () use ($request) {

            $equipeId = session('equipe_id');
            $lastId   = (int) $request->input('lastId', 0);
            $deadline = time() + 55;

            while (time() < $deadline) {

                $nouvelles = AlerteAffectation::with(['alerte.citoyen', 'serviceSource'])
                    ->where('service_destination_id', $equipeId)
                    ->where('id', '>', $lastId)
                    ->orderBy('id')
                    ->get();

                if ($nouvelles->isNotEmpty()) {
                    foreach ($nouvelles as $aff) {
                        $alerte  = $aff->alerte;
                        $payload = [
                            'id'          => $aff->id,
                            'statut'      => $aff->statut,
                            'commentaire' => $aff->commentaire,
                            'created_at'  => $aff->created_at->diffForHumans(),
                            'alerte'      => $alerte ? [
                                'id'          => $alerte->id,
                                'titre'       => $alerte->titre,
                                'localisation'=> $alerte->localisation,
                                'latitude'    => $alerte->latitude,
                                'longitude'   => $alerte->longitude,
                                'type_alerte' => $alerte->type_alerte,
                            ] : null,
                            'service_source' => $aff->serviceSource?->nom,
                        ];

                        echo "data: " . json_encode($payload) . "\n\n";
                        $lastId = $aff->id;
                    }

                    if (ob_get_level()) ob_flush();
                    flush();
                }

                echo ": heartbeat\n\n";
                if (ob_get_level()) ob_flush();
                flush();

                sleep(3);
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);

        return $response;
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

        $alerte->equipe_id            = $request->service_destination_id;
        $alerte->statut_intervention  = 'affectee';
        $alerte->save();

        return back()->with('success', 'Alerte affectée avec succès.');
    }

    public function interventionsJournalieres()
    {
        $interventions = \DB::table('alerte_interventions')
            ->join('alertes',   'alertes.id',   '=', 'alerte_interventions.alerte_id')
            ->join('services',  'services.id',  '=', 'alerte_interventions.equipe_id')
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
}
