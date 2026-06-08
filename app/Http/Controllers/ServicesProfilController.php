<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Services;

class ServicesProfilController extends Controller
{
    // Affichage du profil du service connecté
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()
                ->route('services.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $service = Services::find(session('service_id'));

        return view('services.profil', compact('service'));
    }

    // Mise à jour du profil
    public function update(Request $request)
    {
        $service = Services::findOrFail(session('service_id'));

        $validated = $request->validate([
            'nom'           => 'required|string|max:255',
            'email'         => 'required|email|unique:services,email,' . $service->id,
            'adresse'       => 'nullable|string|max:255',
            'telephone'     => 'nullable|string|max:20',
            'photo_profil'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Infos de base
        $service->nom       = $validated['nom'];
        $service->email     = $validated['email'];
        $service->adresse   = $validated['adresse'] ?? $service->adresse;
        $service->telephone = $validated['telephone'] ?? $service->telephone;

        // Photo
        if ($request->hasFile('photo_profil')) {

            // Créer le dossier si inexistant
            if (!is_dir('uploads/services')) {
                mkdir('uploads/services', 0755, true);
            }

            // Supprimer l'ancienne photo
            if ($service->photo_profil && file_exists($service->photo_profil)) {
                unlink($service->photo_profil);
            }

            $file = $request->file('photo_profil');

            $filename = time() . '_' .
                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                '.' . $file->getClientOriginalExtension();

            // ⚠️ PAS de public_path()
            $file->move('uploads/services', $filename);

            $service->photo_profil = 'uploads/services/' . $filename;
        }

        $service->save();

        // Mise à jour session
        session([
            'service_nom'       => $service->nom,
            'service_adresse'   => $service->adresse,
            'service_telephone' => $service->telephone,
            'service_email'     => $service->email,
            'service_photo'     => $service->photo_profil,
        ]);

        return redirect()
            ->route('services.profil')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
