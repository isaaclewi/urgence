<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\servicesProposes;

class MesAlertesController extends Controller
{
    public function index()
    {
        if (session()->has('citoyen_id')) {
            $citoyen  = \App\Models\Citoyens::find(session('citoyen_id'));
            $services = servicesProposes::all();

            return view('MesAlertes', compact('citoyen', 'services'));
        }

        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }
}
