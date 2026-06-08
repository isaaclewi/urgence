<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\vaccinEnfant;
use Carbon\Carbon;

class ProgrammeVaccinEnfantController extends Controller
{
    public function index()
    {
        // Récupérer tous les vaccins destinés aux enfants
        $vaccins = vaccinEnfant::all();

        // Convertir les dates en objets Carbon pour une meilleure manipulation
        foreach ($vaccins as $vaccin) {
            if (!empty($vaccin->date_fabrication)) {
                $vaccin->date_fabrication = Carbon::parse($vaccin->date_fabrication);
            }
            if (!empty($vaccin->date_expiration)) {
                $vaccin->date_expiration = Carbon::parse($vaccin->date_expiration);
            }
        }

        // Envoi des données à la vue
        return view('vaccinationEnfant', compact('vaccins'));
    }
}
