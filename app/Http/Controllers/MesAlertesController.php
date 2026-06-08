<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MesAlertesController extends Controller
{
    public function index()
{
    // Vérifier si un citoyen est connecté
    if (session()->has('citoyen_id')) {
        $citoyen = \App\Models\Citoyens::find(session('citoyen_id'));
        return view('MesAlertes', compact('citoyen'));
    }

    // Si aucun citoyen en session, rediriger vers login
    return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
}
}
