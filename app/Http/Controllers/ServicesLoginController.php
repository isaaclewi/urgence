<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Services;

class ServicesLoginController extends Controller
{
    /**
     * Affiche le formulaire login
     * Deux boutons de sélection : "Service" ou "Équipe"
     */
    public function showLoginForm()
    {
        return view('services.login');
    }

    /**
     * Traitement login unifié — service parent OU équipe
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'compte_type' => 'required|in:service,equipe',
        ]);

        if ($request->compte_type === 'equipe') {
            // ─── Connexion équipe ───────────────────────────────────────
            // Une équipe est un Services dont type_compte = 'equipe'
            $equipe = Services::where('email', $request->email)
                               ->where('type_compte', 'equipe')
                               ->first();

            if (!$equipe) {
                return back()->withErrors(['email' => 'Aucune équipe trouvée avec cet email.'])->withInput();
            }

            // Mot de passe par défaut "12345678" ou personnalisé
            if (!Hash::check($request->password, $equipe->password)) {
                return back()->withErrors(['password' => 'Mot de passe incorrect.'])->withInput();
            }

            if ($equipe->etat_compte !== 'actif') {
                return back()->withErrors(['email' => 'Ce compte équipe est désactivé.'])->withInput();
            }

            session([
                'equipe_id'        => $equipe->id,
                'equipe_nom'       => $equipe->nom,
                'equipe_email'     => $equipe->email,
                'equipe_telephone' => $equipe->telephone,
                'equipe_adresse'   => $equipe->adresse,
                'equipe_photo'     => $equipe->photo_profil,
                // parent service
                'equipe_parent_id' => $equipe->parent_service_id,
                'compte_type'      => 'equipe',
            ]);

            return redirect()->route('equipe.dashboard')->with('success', 'Connexion équipe réussie.');

        } else {
            // ─── Connexion service parent ───────────────────────────────
            $service = Services::where('email', $request->email)
                                ->whereIn('type_compte', ['service', null])
                                ->whereNull('parent_service_id')
                                ->first();

            if (!$service) {
                // fallback : chercher sans filtre type_compte (anciens enregistrements)
                $service = Services::where('email', $request->email)
                                   ->whereNull('parent_service_id')
                                   ->first();
            }

            if (!$service) {
                return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->withInput();
            }

            if (!Hash::check($request->password, $service->password)) {
                return back()->withErrors(['password' => 'Email ou mot de passe incorrect.'])->withInput();
            }

            if ($service->etat_compte !== 'actif') {
                return back()->withErrors(['email' => 'Ce compte service est désactivé.'])->withInput();
            }

            session([
                'service_id'        => $service->id,
                'service_nom'       => $service->nom,
                'service_photo'     => $service->photo_profil,
                'service_adresse'   => $service->adresse,
                'service_telephone' => $service->telephone,
                'service_email'     => $service->email,
                'compte_type'       => 'service',
            ]);

            return redirect()->route('services.compte')->with('success', 'Connexion réussie.');
        }
    }

    /**
     * Déconnexion (service ET équipe)
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('services.login')->with('success', 'Déconnecté avec succès.');
    }
}
