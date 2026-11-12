<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Services;
use App\Models\alertes;
use App\Models\AlerteAffectation;
use App\Models\AlerteIntervention;

class EquipeDashboardController extends Controller
{
    /**
     * Vérifie que l'équipe est connectée, sinon redirige
     */
    private function checkAuth()
    {
        if (!session()->has('equipe_id')) {
            return redirect()->route('services.login')
                             ->with('error', 'Veuillez vous connecter en tant qu\'équipe.');
        }
        return null;
    }

    /**
     * Dashboard principal de l'équipe
     */
    public function index()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $equipeId  = session('equipe_id');
        $equipe    = Services::findOrFail($equipeId);
        $parentId  = $equipe->parent_service_id;
        $parent    = $parentId ? Services::find($parentId) : null;

        // Alertes affectées à cette équipe via alerte_affectations
        $affectations = AlerteAffectation::with('alerte.citoyen')
            ->where('service_destination_id', $equipeId)
            ->where('statut', '!=', 'annulee')
            ->orderByDesc('created_at')
            ->get();

        // Statistiques rapides
        $stats = [
            'total'        => $affectations->count(),
            'transmise'    => $affectations->where('statut', 'transmise')->count(),
            'en_cours'     => $affectations->where('statut', 'en_cours')->count(),
            'terminee'     => $affectations->where('statut', 'terminee')->count(),
        ];

        // Dernière alerte affectée avec coordonnées (pour la carte)
        $derniereAlerte = $affectations
            ->filter(fn($a) => $a->alerte && $a->alerte->latitude)
            ->first();

        return view('equipes.dashboard', compact(
            'equipe', 'parent', 'affectations', 'stats', 'derniereAlerte'
        ));
    }

    /**
     * Liste complète des alertes affectées à l'équipe
     */
    public function alertes()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $equipeId = session('equipe_id');
        $equipe   = Services::findOrFail($equipeId);

        $affectations = AlerteAffectation::with(['alerte.citoyen', 'serviceSource'])
            ->where('service_destination_id', $equipeId)
            ->where('statut', '!=', 'annulee')
            ->orderByDesc('created_at')
            ->get();

        return view('equipes.alertes', compact('equipe', 'affectations'));
    }

    /**
     * Détail d'une alerte + carte
     */
    public function alerteDetail($id)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $equipeId    = session('equipe_id');
        $equipe      = Services::findOrFail($equipeId);
        $affectation = AlerteAffectation::with(['alerte.citoyen', 'serviceSource'])
            ->where('service_destination_id', $equipeId)
            ->where('statut', '!=', 'annulee')
            ->where('id', $id)
            ->firstOrFail();

        return view('equipes.alerte_detail', compact('equipe', 'affectation'));
    }

    /**
     * Mettre à jour le statut d'une affectation
     */
    public function updateStatut(Request $request, $id)
    {
        if ($redir = $this->checkAuth()) return $redir;

        $request->validate([
            'statut'      => 'required|in:transmise,en_cours,terminee',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $affectation = AlerteAffectation::with('alerte')->findOrFail($id);

        // Sécurité : l'équipe ne peut modifier que ses propres affectations (et pas une affectation annulée)
        if ($affectation->service_destination_id != session('equipe_id') || $affectation->statut === 'annulee') {
            abort(403);
        }

        // Déroulement : transmise → en_cours → terminee, sans retour en arrière
        $ordre = ['transmise' => 0, 'en_cours' => 1, 'terminee' => 2];

        if ($ordre[$request->statut] < ($ordre[$affectation->statut] ?? 0)) {
            return back()->with('error', 'Impossible de revenir à une étape précédente.');
        }

        DB::transaction(function () use ($affectation, $request) {
            $affectation->statut = $request->statut;
            $affectation->save();

            $alerte = $affectation->alerte;

            if ($request->statut === 'terminee') {
                $alerte->update(['statut' => 'terminee', 'statut_intervention' => 'terminee']);
            } elseif ($request->statut === 'en_cours') {
                $alerte->update(['statut' => 'pris en charge', 'statut_intervention' => 'en_cours']);
            }

            // Trace de l'intervention : c'est elle qui alimente la page « Interventions » du service,
            // les rapports PDF et les exports (avant, rien ne la créait).
            if (in_array($request->statut, ['en_cours', 'terminee'], true)) {
                $donnees = [
                    'statut'    => $request->statut,
                    'latitude'  => $alerte->latitude,
                    'longitude' => $alerte->longitude,
                ];

                if ($request->filled('commentaire')) {
                    $donnees['commentaire'] = $request->commentaire;
                }

                AlerteIntervention::updateOrCreate(
                    ['alerte_id' => $alerte->id, 'equipe_id' => $affectation->service_destination_id],
                    $donnees
                );
            }
        });

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Page profil de l'équipe
     */
    public function profil()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $equipe = Services::findOrFail(session('equipe_id'));
        return view('equipes.profil', compact('equipe'));
    }
}
