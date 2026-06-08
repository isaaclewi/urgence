<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\vaccinEnfant;
use App\Models\Services;

class ServiceVaccinEnfantController extends Controller
{
    // --- Liste des vaccins enfants ---
    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Veuillez vous connecter.');
        }

        $vaccins = vaccinEnfant::orderBy('id', 'desc')->paginate(10);
        $service = Services::find(session('service_id'));

        return view('services.vaccinationEnfants', compact('vaccins', 'service'));
    }

    // --- Formulaire d'ajout ---
    public function create()
    {
        $service = session('service');
        return view('services.vaccinationEnfantsCreate', compact('service'));
    }

    // --- Enregistrement d’un nouveau vaccin ---
    public function store(Request $request)
    {
        $request->validate([
            'nom_vaccin' => 'required|string|max:255',
            'fabricant' => 'nullable|string|max:255',
            'nombre_doses' => 'nullable|integer|min:1',
            'voie_administration' => 'nullable|string|max:100',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
            'age_cible_min' => 'nullable|integer|min:0',
            'age_cible_max' => 'nullable|integer|gte:age_cible_min',
            'vaccination_obligatoire' => 'boolean',
            'programme_id' => 'nullable|integer',
        ]);

        VaccinEnfant::create($request->all());

        return redirect()->route('services.vaccinationEnfantsIndex')
                         ->with('success', 'Vaccin ajouté avec succès.');
    }

    // --- Formulaire d’édition ---
    public function edit($id)
    {
        $vaccin = VaccinEnfant::findOrFail($id);
        $service = session('service');
        return view('services.vaccinationEnfantsEdit', compact('vaccin', 'service'));
    }

    // --- Mise à jour d’un vaccin ---
    public function update(Request $request, $id)
    {
        $vaccin = VaccinEnfant::findOrFail($id);

        $request->validate([
            'nom_vaccin' => 'required|string|max:255',
            'fabricant' => 'nullable|string|max:255',
            'nombre_doses' => 'nullable|integer|min:1',
            'voie_administration' => 'nullable|string|max:100',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_fabrication',
            'age_cible_min' => 'nullable|integer|min:0',
            'age_cible_max' => 'nullable|integer|gte:age_cible_min',
            'vaccination_obligatoire' => 'boolean',
            'programme_id' => 'nullable|integer',
        ]);

        $vaccin->update($request->all());

        return redirect()->route('services.vaccinationEnfantsIndex')
                         ->with('success', 'Vaccin mis à jour avec succès.');
    }

    // --- Suppression ---
    public function destroy($id)
    {
        $vaccin = VaccinEnfant::findOrFail($id);
        $vaccin->delete();

        return redirect()->route('services.vaccinationEnfantsIndex')
                         ->with('success', 'Vaccin supprimé avec succès.');
    }
}
