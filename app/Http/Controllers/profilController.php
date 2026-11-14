<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Citoyens;
use App\Support\ImageUpload;

class profilController extends Controller
{
    public function index()
    {
        if (!session()->has('citoyen_id')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $citoyen = Citoyens::find(session('citoyen_id'));

        if (!$citoyen) {
            return redirect()->route('login')->with('error', 'Citoyen introuvable.');
        }

        return view('profil', compact('citoyen'));
    }

    public function update(Request $request)
    {
        if (!session()->has('citoyen_id')) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $citoyen = Citoyens::findOrFail(session('citoyen_id'));

        $validated = $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'required|email|unique:citoyens,email,' . $citoyen->id,
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'password'  => 'nullable|min:6',
            'photo'     => ImageUpload::REGLE_IMAGE,
        ], array_merge(ImageUpload::MESSAGES, [
            'password.min' => 'Le mot de passe doit contenir au minimum 6 caractères.',
            'email.unique' => 'Cet email est déjà utilisé par un autre compte.',
        ]));

        $citoyen->nom       = $validated['nom'];
        $citoyen->email     = $validated['email'];
        $citoyen->adresse   = $validated['adresse'];
        $citoyen->telephone = $validated['telephone'];

        if (!empty($validated['password'])) {
            $citoyen->password = Hash::make($validated['password']);
        }

        // Nouvelle photo : envoyée (et réduite) AVANT d'enregistrer, l'ancienne n'est supprimée qu'ensuite.
        $anciennePhoto = null;
        if ($request->hasFile('photo')) {
            $anciennePhoto         = $citoyen->photo_profil;
            $citoyen->photo_profil = ImageUpload::storeImage($request->file('photo'), 'photo', 'photo');
        }

        $citoyen->save();

        if ($anciennePhoto) {
            ImageUpload::supprimer($anciennePhoto, 'photo');
        }

        session([
            'citoyen_nom'       => $citoyen->nom,
            'citoyen_adresse'   => $citoyen->adresse,
            'citoyen_telephone' => $citoyen->telephone,
            'citoyen_email'     => $citoyen->email,
            'citoyen_photo'     => $citoyen->photo_profil,
        ]);

        return redirect()->route('profilController')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
