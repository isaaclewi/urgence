<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonProgrammeVaccin;
use App\Models\Services;

class VaccinationController extends Controller
{
    // Affichage de tous les programmes
   public function index()
{
    if (! session()->has('service_id')) {
        return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
    }

    $programmes = MonProgrammeVaccin::orderBy('id', 'desc')->paginate(10);
    $service = Services::find(session('service_id'));

    return view('services.vaccinationIndex', compact('programmes', 'service'));
}


    // Affichage du formulaire d'ajout
    public function create()
    {
        $service = session('service');
        return view('services.vaccinationCreate', compact('service'));
    }

    // Sauvegarde d'un nouveau programme
    public function store(Request $request)
    {
        $request->validate([
            'nom_programme' => 'required|string|max:255',
            'age_cible' => 'nullable|string|max:50',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'nb_vaccins' => 'nullable|integer|min:0',
            'organisme_resp' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:100',
        ]);

        MonProgrammeVaccin::create($request->all());

        return redirect()->route('services.vaccinationIndex')
                         ->with('success', 'Programme ajouté avec succès.');
    }

    // Formulaire d'édition
    public function edit($id)
    {
        $programme = MonProgrammeVaccin::findOrFail($id);
        $service = session('service');
        return view('services.vaccinationEdit', compact('programme', 'service'));
    }

    // Mise à jour du programme
    public function update(Request $request, $id)
    {
        $programme = MonProgrammeVaccin::findOrFail($id);

        $request->validate([
            'nom_programme' => 'required|string|max:255',
            'age_cible' => 'nullable|string|max:50',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'nb_vaccins' => 'nullable|integer|min:0',
            'organisme_resp' => 'nullable|string|max:255',
            'categorie' => 'nullable|string|max:100',
        ]);

        $programme->update($request->all());

        return redirect()->route('services.vaccinationIndex')
                         ->with('success', 'Programme mis à jour avec succès.');
    }

    // Suppression du programme
    public function destroy($id)
    {
        $programme = MonProgrammeVaccin::findOrFail($id);
        $programme->delete();

        return redirect()->route('services.vaccinationIndex')
                         ->with('success', 'Programme supprimé avec succès.');
    }
}
