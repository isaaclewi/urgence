<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Citoyens;

class ServicesCompteController extends Controller
{
    public function index()
    {
        // Vérifie la session service
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $serviceId = session('service_id');
        $service = \App\Models\Services::find($serviceId);

        // ---- 🔢 STATS ----
        $stats = [
            'urgences_en_cours' => alertes::where('statut', 'en attente')
                ->where('services_id', $serviceId)
                ->count(),

            'urgences_resolues' => alertes::where('statut', 'resolue')
                ->where('services_id', $serviceId)
                ->count(),

            // Citoyens actifs (connectés ou ayant fait un signalement récemment)
            'citoyens_actifs' => Citoyens::where('etat_compte', 'actif')->count(),

            
        ];

        // ---- 🆕 ALERTES RÉCENTES ----
        $recentAlerts = alertes::latest()->take(5)->get()->map(function ($a) {
            return [
                'title' => $a->titre ?? 'Alerte sans titre',
                'type' => ucfirst($a->type ?? 'inconnu'),
                'time' => $a->created_at->diffForHumans(),
                'location' => $a->localisation ?? '—',
            ];
        });

        return view('services.compte', compact('service', 'stats', 'recentAlerts'));
    }
}
