<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminRegisterController extends Controller
{
    public function create()
    {
        return view('admin.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'matricule' => 'required|string|max:50|unique:agents,matricule', // ← admins → agents
    'nom'       => 'required|string|max:255',
    'prenom'    => 'required|string|max:255',
    'sexe'      => 'required|in:H,F',
    'adresse'   => 'required|string|max:255',
    'email'     => 'required|email|unique:agents,email',             // ← admins → agents
    'telephone' => 'required|string|max:20',
    'mot_passe' => 'required|min:6',
    'role'      => 'required|string|max:50',
    'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);
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

        // Photo (racine du projet)
        if ($request->hasFile('photo')) {

            if (!is_dir('uploads/photo')) {
                mkdir('uploads/photo', 0755, true);
            }

            $file = $request->file('photo');

            $filename = time() . '_' .
                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                '.' . $file->getClientOriginalExtension();

            $file->move('uploads/photo', $filename);

            $admin->photo_profil = 'uploads/photo/' . $filename;
        }

        $admin->save();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Administrateur créé avec succès !');
    }
}
