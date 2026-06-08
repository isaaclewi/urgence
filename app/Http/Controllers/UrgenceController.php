<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Services;

class UrgenceController extends Controller
{
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $serviceId = session('service_id');

        // On récupère le service connecté
        $service = Services::find($serviceId);

        // 🔹 Récupération des alertes selon le rôle
        // Exemple : si le service est police, on prend uniquement les alertes de type 'police'
        $alertes = alertes::with('citoyen')
            ->when($service->role, function($query) use ($service) {
                $query->where('type_alerte', $service->role);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('services.urgenceSignalee', compact('service', 'alertes'));
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
}
