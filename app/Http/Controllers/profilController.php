<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Citoyens;

class profilController extends Controller
{
    public function index()
    {
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));
            return view('profil', compact('citoyen'));
        }

        return redirect()->route('login')->with('error', 'Vous devez être connecté.');
    }

    public function update(Request $request)
    {
        $citoyen = Citoyens::findOrFail(session('citoyen_id'));

        $validated = $request->validate([
    'nom'       => 'required|string|max:255',
    'email'     => 'required|email|unique:citoyens,email,' . $citoyen->id,
    'adresse'   => 'required|string|max:255',
    'telephone' => 'required|string|max:20',
    'password'  => 'nullable|min:6',
    'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
], [
    'password.min' => 'Le mot de passe doit contenir au minimum 6 caractères.',
    'photo.image' => 'La photo doit être une image valide.',
    'photo.max'   => 'La taille de la photo ne doit pas dépasser 2 Mo.',
    'email.unique'=> 'Cet email est déjà utilisé par un autre compte.',
]);


        // Mise à jour infos
        $citoyen->nom       = $validated['nom'];
        $citoyen->email     = $validated['email'];
        $citoyen->adresse   = $validated['adresse'];
        $citoyen->telephone = $validated['telephone'];

        // Mot de passe
        if (!empty($validated['password'])) {
            $citoyen->password = Hash::make($validated['password']);
        }

        // Photo de profil (racine du projet)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();

            // dossier : /uploads/photo (à la racine)
            $file->move('uploads/photo', $filename);

            // chemin stocké en base
            $citoyen->photo_profil = 'uploads/photo/' . $filename;
        }

        $citoyen->save();

        // Mise à jour session
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
