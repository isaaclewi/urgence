<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonProgrammeVaccin;
use Carbon\Carbon;


class ProgrammeVaccinController extends Controller
{
   public function index()
{
    $programmes = \App\Models\MonProgrammeVaccin::where('categorie', 'pandemie')->get();

    // Convertir les dates en objets Carbon pour éviter l'erreur "format() on string"
    foreach ($programmes as $programme) {
        if (!empty($programme->date_debut)) {
            $programme->date_debut = \Carbon\Carbon::parse($programme->date_debut);
        }
        if (!empty($programme->date_fin)) {
            $programme->date_fin = \Carbon\Carbon::parse($programme->date_fin);
        }
    }

    return view('vaccinationPandemie', compact('programmes'));
}

}
