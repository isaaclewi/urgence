<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alertes;

class AlertePompierController extends Controller
{
    public function index()
    {
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter.');
        }

        $citoyen = \App\Models\Citoyens::find(session('citoyen_id'));
        return view('alertePompier', compact('citoyen'));
    }

    public function store(Request $request)
    {
        // Validation des champs
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type_alerte' => 'required|string',
            'localisation' => 'required|string',
            'media_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'media_vocal' => 'nullable|string', // base64
        ]);

        // --- Gestion photo ---
        $photoPath = null;
        if ($request->hasFile('media_photo')) {
            if (!is_dir('uploads/alertes/photos')) {
                mkdir('uploads/alertes/photos', 0755, true);
            }

            $file = $request->file('media_photo');
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                        . '.' . $file->getClientOriginalExtension();

            $file->move('uploads/alertes/photos', $filename);
            $photoPath = 'uploads/alertes/photos/' . $filename;
        }

        // --- Gestion vocal base64 ---
        $vocalPath = null;
        if ($request->media_vocal) {
            $data = preg_replace('/^data:audio\/\w+;base64,/', '', $request->media_vocal);
            $data = str_replace(' ', '+', $data);
            $audioContent = base64_decode($data);

            if (!is_dir('uploads/alertes/vocaux')) {
                mkdir('uploads/alertes/vocaux', 0755, true);
            }

            $filename = 'vocal_' . time() . '.webm';
            file_put_contents('uploads/alertes/vocaux/' . $filename, $audioContent);
            $vocalPath = 'uploads/alertes/vocaux/' . $filename;
        }

        // --- Création de l’alerte ---
        alertes::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_heure' => now(),
            'localisation' => $request->localisation,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'media_photo' => $photoPath,
            'media_vocal' => $vocalPath,
            'statut' => 'en attente',
            'type_alerte' => $request->type_alerte,
            'citoyen_id' => session('citoyen_id'),
            'services_id' => null,
        ]);

        return redirect()->route('SuiviAlertesController')
                         ->with('success', 'Alerte envoyée avec succès.');
    }
}
