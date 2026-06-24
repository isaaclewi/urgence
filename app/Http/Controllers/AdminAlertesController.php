<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;
use App\Models\Citoyens;

class AdminAlertesController extends Controller
{
    public function index()
    {
        // Vérifier si l’admin est connecté
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Veuillez vous connecter.');
        }

        // Charger toutes les alertes (avec les infos du citoyen et service)
        $alertes = alertes::with(['citoyen', 'service'])
    ->orderBy('created_at', 'desc')
    ->get();

        return view('admin.alertes', compact('alertes'));
    }
}
