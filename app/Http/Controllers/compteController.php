<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;

class compteController extends Controller
{
    public function index()
    {
        // Vérifier si l’utilisateur est connecté
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter.');
        }

        // Récupérer le citoyen
        $citoyen = Citoyens::find(session('citoyen_id'));

        // Vérifier l’état du compte directement en base
        if ($citoyen && $citoyen->etat_compte === 'actif') {
            return view('compte', compact('citoyen'));
        }

        // Si inactif ou non trouvé
        return redirect()->route('accueil')->with('error', 'Votre compte est inactif. Veuillez contacter le support.');
    }
}
