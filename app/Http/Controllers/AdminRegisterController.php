<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Support\ImageUpload;

class AdminRegisterController extends Controller
{
    public function create()
    {
        return view('admin.register');
    }

    public function store(Request $request)
    {
        $table = (new Admins())->getTable();

        $validated = $request->validate([
            // Avant : « unique:agents,... » -> vérifiait la table du personnel et non celle des admins.
            'matricule' => ['required', 'string', 'max:50', Rule::unique($table, 'matricule')],
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'sexe'      => 'required|in:H,F',
            'adresse'   => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique($table, 'email')],
            'telephone' => 'required|string|max:20',
            'mot_passe' => 'required|min:6',
            'role'      => 'required|string|max:50',
            'photo'     => ImageUpload::REGLE_IMAGE,
        ], array_merge(ImageUpload::MESSAGES, [
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
            'email.unique'     => 'Cet email est déjà utilisé.',
            'mot_passe.min'    => 'Le mot de passe doit contenir au minimum 6 caractères.',
        ]));

        $admin = new Admins();
        $admin->matricule   = $validated['matricule'];
        $admin->nom         = $validated['nom'];
        $admin->prenom      = $validated['prenom'];
        $admin->sexe        = $validated['sexe'];
        $admin->adresse     = $validated['adresse'];
        $admin->email       = $validated['email'];
        $admin->telephone   = $validated['telephone'];
        $admin->etat_compte = 'actif';
        $admin->mot_passe   = Hash::make($validated['mot_passe']);
        $admin->role        = $validated['role'];

        if ($request->hasFile('photo')) {
            $admin->photo_profil = ImageUpload::storeImage($request->file('photo'), 'admins/photos', 'photo');
        }

        $admin->save();

        return redirect()
            ->route('admin.users')
            ->with('success', 'Administrateur créé avec succès !');
    }
}
