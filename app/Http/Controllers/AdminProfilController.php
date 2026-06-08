<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Admins;

class AdminProfilController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $admin = Admins::find(session('admin_id'));

        return view('admin.profil', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Admins::findOrFail(session('admin_id'));

        $validated = $request->validate([
            'nom'       => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . $admin->id,
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'mot_passe' => 'nullable|min:6',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // MAJ infos
        $admin->nom       = $validated['nom'];
        $admin->email     = $validated['email'];
        $admin->adresse   = $validated['adresse'];
        $admin->telephone = $validated['telephone'];

        // MAJ mot de passe
        if (!empty($validated['mot_passe'])) {
            $admin->mot_passe = Hash::make($validated['mot_passe']);
        }

        // MAJ photo (racine du projet)
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

        // MAJ session
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

    public function edit()
    {
        $admin = Admins::find(session('admin_id'));
        return view('admin.edit_profil', compact('admin'));
    }
}
