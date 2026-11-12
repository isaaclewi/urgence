<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\alertes;
use App\Models\Citoyens;
use App\Models\Services;

class AdminCompteController extends Controller
{
    public function index()
    {
        // Comptes citoyens qui ne sont pas actifs (y compris état vide)
        $nonActifs = fn ($q) => $q->whereNull('etat_compte')->orWhere('etat_compte', '!=', 'actif');

        $stats = [
            'citoyens'        => Citoyens::count(),
            'a_valider'       => Citoyens::where($nonActifs)->count(),
            'services'        => Services::whereNull('parent_service_id')->count(),
            'equipes'         => Services::where('type_compte', 'equipe')->count(),
            'actualites'      => Actualite::count(),
            'alertes'         => alertes::count(),
            'alertes_attente' => alertes::where('statut', 'en attente')->count(),
            'alertes_24h'     => alertes::where('created_at', '>=', now()->subDay())->count(),
        ];

        $recentes = alertes::with(['citoyen:id,nom,prenom', 'service:id,nom'])
            ->latest('created_at')->limit(8)->get();

        $aValider = Citoyens::where($nonActifs)->latest('created_at')->limit(5)
            ->get(['id', 'nom', 'prenom', 'matricule', 'created_at']);

        return view('admin.compte', compact('stats', 'recentes', 'aValider'));
    }
}
