<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actualite;
use App\Models\Citoyens;

class ActualitesController extends Controller
{
    public function index()
    {
        // Vérifier si un citoyen est connecté
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));

            if (!$citoyen) {
                return redirect()->route('login')->with('error', 'Citoyen introuvable.');
            }

            // Récupérer les actualités du service du citoyen
            $actualites = Actualite::orderBy('created_at','desc')->get();


            return view('actualites', compact('citoyen', 'actualites'));
        }

        // Si aucun citoyen en session, rediriger vers login
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }
}
