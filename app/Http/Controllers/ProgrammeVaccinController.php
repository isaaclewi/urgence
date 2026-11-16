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

    \App\Support\SuiviSchema::assurer();
    $suivis = \App\Models\InscriptionVaccin::where('citoyen_id', (int) session('citoyen_id'))->get()->keyBy('programme_id');

    return view('vaccinationPandemie', compact('programmes', 'suivis'));
}

}
