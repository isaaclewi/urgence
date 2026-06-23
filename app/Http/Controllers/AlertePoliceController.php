<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\alertes;

class AlertePoliceController extends Controller
{
    public function index()
    {
        if (!session()->has('citoyen_id')) {
            return redirect()->route('accueil')->with('error', 'Veuillez vous connecter.');
        }

        $citoyen = \App\Models\Citoyens::find(session('citoyen_id'));
        return view('alertePolice', compact('citoyen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'required|string',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'type_alerte'  => 'required|string',
            'localisation' => 'required|string',
            'media_photo'  => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'media_vocal'  => 'nullable|string',
        ]);

        //Gestion photo vers Supabase
        $photoPath = null;
        if ($request->hasFile('media_photo')) {
            $file     = $request->file('media_photo');
            $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                        . '.' . $file->getClientOriginalExtension();

            Storage::disk('supabase')->putFileAs('alertes/photos', $file, $filename);
            $photoPath = env('SUPABASE_PUBLIC_URL') . '/alertes/photos/' . $filename;
        }

        //Gestion vocal base64 vers Supabase
        $vocalPath = null;
        if ($request->media_vocal) {
            preg_match('/data:audio\/(.*?);base64,/', $request->media_vocal, $matches);
            $extension    = $matches[1] ?? 'webm';
            $data         = explode(',', $request->media_vocal)[1] ?? '';
            $audioContent = base64_decode($data);
            $filename     = 'vocal_police_' . time() . '.' . $extension;

            Storage::disk('supabase')->put('alertes/vocaux/' . $filename, $audioContent);
            $vocalPath = env('SUPABASE_PUBLIC_URL') . '/alertes/vocaux/' . $filename;
        }

        alertes::create([
            'titre'        => $request->titre,
            'description'  => $request->description,
            'date_heure'   => now(),
            'localisation' => $request->localisation,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'media_photo'  => $photoPath,
            'media_vocal'  => $vocalPath,
            'statut'       => 'en attente',
            'type_alerte'  => $request->type_alerte,
            'citoyen_id'   => session('citoyen_id'),
            'services_id'  => null,
        ]);

        return redirect()->route('SuiviAlertesController')
                         ->with('success', 'Alerte envoyée avec succès.');
    }
}
