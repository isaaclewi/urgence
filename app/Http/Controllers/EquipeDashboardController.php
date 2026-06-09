<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Services;
use App\Models\alertes;
use App\Models\AlerteAffectation;

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
            'statut' => 'required|in:transmise,en_cours,terminee',
        ]);

        $affectation = AlerteAffectation::findOrFail($id);

        // Sécurité : l'équipe ne peut modifier que ses propres affectations
        if ($affectation->service_destination_id != session('equipe_id')) {
            abort(403);
        }

        $affectation->statut = $request->statut;
        $affectation->save();

        // Si terminée, mettre à jour l'alerte aussi
        if ($request->statut === 'terminee') {
            $affectation->alerte->update(['statut' => 'terminee']);
        } elseif ($request->statut === 'en_cours') {
            $affectation->alerte->update(['statut' => 'pris en charge']);
        }

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
