<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Services;

class ServicesProfilController extends Controller
{
    public function index()
    {
        if (!session()->has('service_id')) {
            return redirect()
                ->route('services.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $service = Services::find(session('service_id'));

        if (!$service) {
            return redirect()
                ->route('services.login')
                ->with('error', 'Service introuvable.');
        }

        return view('services.profil', compact('service'));
    }

    public function update(Request $request)
    {
        if (!session()->has('service_id')) {
            return redirect()
                ->route('services.login')
                ->with('error', 'Vous devez être connecté.');
        }

        $service = Services::findOrFail(session('service_id'));

        $validated = $request->validate([
            'nom'          => 'required|string|max:255',
            'email'        => 'required|email|unique:services,email,' . $service->id,
            'adresse'      => 'nullable|string|max:255',
            'telephone'    => 'nullable|string|max:20',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $service->nom       = $validated['nom'];
        $service->email     = $validated['email'];
        $service->adresse   = $validated['adresse'] ?? $service->adresse;
        $service->telephone = $validated['telephone'] ?? $service->telephone;

        // Upload photo vers Supabase Storage
        if ($request->hasFile('photo_profil')) {
            $file = $request->file('photo_profil');

            $filename = Str::uuid() . '.' . strtolower($file->extension() ?: $file->getClientOriginalExtension());

            // Supprimer l'ancienne photo si elle existe
            if ($service->photo_profil) {
                $oldFilename = basename($service->photo_profil);
                Storage::disk('supabase')->delete('services/' . $oldFilename);
            }

            // Upload vers Supabase
            Storage::disk('supabase')->putFileAs('services', $file, $filename);

            // URL publique
            $service->photo_profil = config('filesystems.disks.supabase.public_url') . '/services/' . $filename;
        }

        $service->save();

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

    /**
     * Changement du mot de passe du compte service connecté.
     */
    public function password(Request $request)
    {
        if (! session()->has('service_id')) {
            return redirect()->route('services.login')->with('error', 'Vous devez être connecté.');
        }

        $service = Services::findOrFail(session('service_id'));

        $request->validate([
            'mot_de_passe_actuel' => 'required|string',
            'nouveau_mot_de_passe' => 'required|string|min:8|confirmed',
        ], [
            'nouveau_mot_de_passe.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
            'nouveau_mot_de_passe.min'       => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
        ]);

        if (! Hash::check($request->mot_de_passe_actuel, $service->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Le mot de passe actuel est incorrect.']);
        }

        $service->password = Hash::make($request->nouveau_mot_de_passe);
        $service->save();

        $request->session()->regenerate();

        return redirect()->route('services.profil')->with('success', 'Mot de passe modifié avec succès.');
    }
}
