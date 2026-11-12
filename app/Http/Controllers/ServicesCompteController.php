<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Models\Services;
use App\Services\KpiService;
use App\Support\AlertesScope;

class ServicesCompteController extends Controller
{
    /**
     * Tableau de bord du service.
     * Avant : les compteurs filtraient sur services_id (toujours vide pour les alertes
     * publiques) et sur le statut « resolue » (qui n'existe pas), et la liste « récentes »
     * montrait les alertes de tous les services.
     */
    public function index(KpiService $kpi)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $service = Services::findOrFail(session('service_id'));

        $compter = fn (array $statuts) => AlertesScope::pourService($service)->whereIn('statut', $statuts)->count();

        $stats = [
            'en_attente'      => $compter(['en attente']),
            'en_cours'        => $compter(['pris en charge', 'transmise']),
            'terminees'       => $compter(['terminee']),
            'citoyens_actifs' => Citoyens::where('etat_compte', 'actif')->count(),
            'delai_moyen'     => '—',
        ];

        try {
            $stats['delai_moyen'] = $kpi->calculer($service, 30)['delaiMoyen'] ?? '—';
        } catch (\Throwable $e) {
            // Le tableau de bord reste utilisable si le calcul des indicateurs échoue.
        }

        $recentes = AlertesScope::pourService($service)->latest()->take(6)->get();

        return view('services.compte', compact('service', 'stats', 'recentes'));
    }

    /**
     * Ancienne fonction de dépôt de pièce d'identité du service : retirée.
     * Elle écrivait dans une colonne inexistante et publiait le document sur une URL publique.
     */
    public function uploadPiece()
    {
        abort(410, 'Cette fonction a été retirée.');
    }
}
