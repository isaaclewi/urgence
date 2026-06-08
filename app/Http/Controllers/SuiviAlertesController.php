<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Citoyens;

class SuiviAlertesController extends Controller
{
    public function index()
    {
        // Vérifier si le citoyen est connecté
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter pour accéder à vos alertes.');
        }

        $citoyen_id = session('citoyen_id');

        // Récupérer toutes les alertes du citoyen
        $alertes = alertes::where('citoyen_id', $citoyen_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mes_alertes', compact('alertes'));
    }
}
