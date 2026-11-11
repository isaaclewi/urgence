<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $service     = Services::findOrFail(session('service_id'));
        $rolesConnus = $this->rolesConnus();

        $alertes = $this->alertesPourService($service, $rolesConnus)
            ->with('citoyen')
            ->orderByDesc('created_at')
            ->get();

        return view('services.urgenceSignalee', compact('service', 'alertes'));
    }

    // ─────────────────────────────────────────────────
    //  Polling JSON — Nouvelles alertes pour le service connecté
    //  GET /services/urgences/stream?lastId=123
    //  (le nom de route "stream" est conservé ; la réponse est maintenant
    //   immédiate, ce qui ne bloque plus php artisan serve ni un worker PHP)
    // ─────────────────────────────────────────────────
    public function stream(Request $request)
    {
        $service = Services::find(session('service_id'));
        abort_if(!$service, 403);

        $lastId = (int) $request->input('lastId', 0);

        $items = $this->alertesPourService($service, $this->rolesConnus())
            ->with('citoyen')
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn ($a) => $this->payloadAlerte($a))
            ->values();

        return response()->json(['items' => $items])
            ->header('Cache-Control', 'no-store');
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

    private function payloadAlerte($alerte): array
    {
        return [
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
    }

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
