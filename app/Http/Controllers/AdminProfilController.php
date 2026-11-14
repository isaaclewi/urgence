<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Admins;
use App\Support\ImageUpload;

class AdminProfilController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::find(session('admin_id'));

        if (!$admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Administrateur introuvable.');
        }

        return view('admin.profil', compact('admin'));
    }

    public function edit()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Vous devez être connecté.');
        }

        // Le formulaire de modification est intégré à la page profil
        return redirect()->route('admin.profil');
    }

    public function update(Request $request)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::findOrFail(session('admin_id'));

        $validated = $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            // Avant : « unique:agents,email » -> mauvaise table (le personnel) au lieu de celle des admins.
            'email'     => ['required', 'email', Rule::unique($admin->getTable(), 'email')->ignore($admin->id)],
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'mot_passe' => 'nullable|min:6',
            'photo'     => ImageUpload::REGLE_IMAGE,
        ], array_merge(ImageUpload::MESSAGES, [
            'email.unique'  => 'Cet email est déjà utilisé par un autre compte.',
            'mot_passe.min' => 'Le mot de passe doit contenir au minimum 6 caractères.',
        ]));

        $admin->nom       = $validated['nom'];
        $admin->prenom    = $validated['prenom'];
        $admin->email     = $validated['email'];
        $admin->adresse   = $validated['adresse'];
        $admin->telephone = $validated['telephone'];

        if (!empty($validated['mot_passe'])) {
            $admin->mot_passe = Hash::make($validated['mot_passe']);
        }

        $anciennePhoto = null;
        if ($request->hasFile('photo')) {
            $anciennePhoto      = $admin->photo_profil;
            $admin->photo_profil = ImageUpload::storeImage($request->file('photo'), 'admins/photos', 'photo');
        }

        $admin->save();

        if ($anciennePhoto) {
            ImageUpload::supprimer($anciennePhoto, 'photo');
        }

        session([
            'admin_nom'       => $admin->nom,
            'admin_adresse'   => $admin->adresse,
            'admin_telephone' => $admin->telephone,
            'admin_email'     => $admin->email,
            'admin_photo'     => $admin->photo_profil,
        ]);

        return redirect()->route('admin.profil')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
