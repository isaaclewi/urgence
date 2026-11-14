<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Citoyens;

class ActualitesController extends Controller
{
    public function index()
    {
        // Vérifier si un citoyen est connecté
        if (!session()->has('citoyen_id')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $citoyen = Citoyens::find(session('citoyen_id'));

        if (!$citoyen) {
            return redirect()->route('login')->with('error', 'Citoyen introuvable.');
        }

        // Pagination : avant, TOUTES les actualités (et leurs images) étaient chargées d'un coup,
        // ce qui rendait la page lourde sur téléphone. Le fil charge la suite au défilement.
        $actualites = Actualite::with('service:id,nom,photo_profil')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(8);

        $derniere = optional($actualites->first())->created_at;

        return view('actualites', compact('citoyen', 'actualites', 'derniere'));
    }
}
