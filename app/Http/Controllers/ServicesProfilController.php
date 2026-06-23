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

            $filename = time() . '_' .
                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                '.' . $file->getClientOriginalExtension();

            // Supprimer l'ancienne photo si elle existe
            if ($service->photo_profil) {
                $oldFilename = basename($service->photo_profil);
                Storage::disk('supabase')->delete('services/' . $oldFilename);
            }

            // Upload vers Supabase
            Storage::disk('supabase')->putFileAs('services', $file, $filename);

            // URL publique
            $service->photo_profil = env('SUPABASE_PUBLIC_URL') . '/services/' . $filename;
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
}
