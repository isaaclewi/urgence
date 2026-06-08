<?php

// dans App\Http\Controllers\bilanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use App\Models\bilanSante;

class bilanController extends Controller
{
    public function index()
    {
        // Vérifier si un citoyen est connecté
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));

            // Récupérer le bilan du citoyen (dernier bilan par exemple)
            $bilan = bilanSante::where('citoyen_id', $citoyen->id)->latest()->first();

            return view('bilanSante', compact('citoyen', 'bilan'));
        }

        // Si aucun citoyen en session, rediriger vers login
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }
}

