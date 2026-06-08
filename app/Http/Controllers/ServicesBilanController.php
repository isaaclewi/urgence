<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Citoyens;
use App\Models\bilanSante;

class ServicesBilanController extends Controller
{
    // Liste et gestion sur une seule page
    public function index()
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Vous devez être connecté.');
        }

        $service = Services::find(session('service_id'));
        $bilans = bilanSante::with('citoyen')->orderBy('id', 'desc')->paginate(10);
        $citoyens = Citoyens::orderBy('nom')->get();

        return view('services.citoyensBilan', compact('service', 'bilans', 'citoyens'));
    }

    // Enregistrement ou mise à jour
    public function store(Request $request)
    {
        $request->validate([
            'citoyen_id' => 'required|exists:citoyens,id',
            'groupe_sanguin' => 'nullable|string|max:10',
            'taille' => 'nullable|string|max:10',
            'poids' => 'nullable|string|max:10',
            'allergies' => 'nullable|string|max:255',
            'antecedents_familiaux' => 'nullable|string|max:255',
            'mode_de_vie' => 'nullable|string|max:255',
            'maladies_chroniques' => 'nullable|string|max:255',
            'maladies_passees_importantes' => 'nullable|string|max:255',
            'interventions_chirurgicales' => 'nullable|string|max:255',
            'antecedents_hospitalisation' => 'nullable|string|max:255',
            'medicaments_pris_actuellement' => 'nullable|string|max:255',
            'listez_vaccins_reçus' => 'nullable|string|max:255',
        ]);

        BilanSante::updateOrCreate(
            ['citoyen_id' => $request->citoyen_id],
            $request->all()
        );

        return redirect()->route('services.citoyensBilan')->with('success', 'Bilan ajouté ou mis à jour avec succès.');
    }

    // Suppression
    public function destroy($id)
    {
        $bilan = BilanSante::findOrFail($id);
        $bilan->delete();

        return redirect()->route('services.citoyensBilan')->with('success', 'Bilan supprimé avec succès.');
    }
}
