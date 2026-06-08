<?php

namespace App\Http\Controllers;

use App\Models\Admins;
use App\Models\BilanSante;
use App\Models\Citoyens;
use Illuminate\Http\Request;

class AdminBilanController extends Controller
{
    // Liste des citoyens avec pagination
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $citoyens = Citoyens::paginate(5);
        $admin = Admins::find(session('admin_id'));

        // Attacher le bilan (si existant) à chaque citoyen pour la vue
        foreach ($citoyens as $citoyen) {
            $citoyen->bilan = BilanSante::where('citoyen_id', $citoyen->id)->first();
        }

        return view('admin.bilanSante', compact('admin', 'citoyens'));
    }

    // Formulaire pour afficher ou éditer un bilan d'un citoyen spécifique
    public function show($id)
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Vous devez être connecté.');
        }

        $citoyen = Citoyens::findOrFail($id);
        $bilan = BilanSante::where('citoyen_id', $id)->first();
        $admin = Admins::find(session('admin_id'));

        return view('admin.bilanForm', compact('admin', 'citoyen', 'bilan'));
    }

    // Enregistrer un nouveau bilan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'citoyen_id' => 'required|exists:citoyens,id',
            'taille' => 'nullable|numeric',
            'poids' => 'nullable|numeric',
            'tension' => 'nullable|string',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        BilanSante::create($validated);

        return redirect()->route('admin.bilanSante')->with('success', 'Bilan de santé enregistré avec succès ✅');
    }

    // Mettre à jour un bilan existant
    public function update(Request $request, $id)
    {
        $bilan = BilanSante::findOrFail($id);

        $validated = $request->validate([
            'taille' => 'nullable|numeric',
            'poids' => 'nullable|numeric',
            'tension' => 'nullable|string',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $bilan->update($validated);

        return redirect()->route('admin.bilanSante')->with('success', 'Bilan de santé mis à jour avec succès ✅');
    }
}
