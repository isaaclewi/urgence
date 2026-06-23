<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        $admin = Admins::find(session('admin_id'));

        return view('admin.edit_profil', compact('admin'));
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
            'email'     => 'required|email|unique:agents,email,' . $admin->id,
            'adresse'   => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'mot_passe' => 'nullable|min:6',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $admin->nom       = $validated['nom'];
        $admin->email     = $validated['email'];
        $admin->adresse   = $validated['adresse'];
        $admin->telephone = $validated['telephone'];

        if (!empty($validated['mot_passe'])) {
            $admin->mot_passe = Hash::make($validated['mot_passe']);
        }

        // Upload photo vers Supabase Storage
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $filename = time() . '_' .
                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                '.' . $file->getClientOriginalExtension();

            // Supprimer l'ancienne photo si elle existe
            if ($admin->photo_profil) {
                $oldFilename = basename($admin->photo_profil);
                Storage::disk('supabase')->delete('photo/' . $oldFilename);
            }

            // Upload vers Supabase
            Storage::disk('supabase')->putFileAs('photo', $file, $filename);

            // URL publique
            $admin->photo_profil = env('SUPABASE_PUBLIC_URL') . '/photo/' . $filename;
        }

        $admin->save();

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
