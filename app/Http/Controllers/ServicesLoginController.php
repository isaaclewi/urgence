<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Services;

class ServicesLoginController extends Controller
{
    //
    // Affiche le formulaire login
    public function showLoginForm()
    {
        return view('services.login'); // fichier blade
    }

    // Traitement login
    public function processLogin(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Récupérer service par email seulement
        $service = Services::where('email', $request->email)->first();

        if ($service && Hash::check($request->password, $service->password)) {
            // mot de passe correct
            session([
                'service_id' => $service->id,
                'service_nom' => $service->nom,
                'service_photo' => $service->photo_profil,
                'service_sexe' => $service->sexe,
                'service_adresse' => $service->adresse,
                'service_telephone' => $service->telephone,
                'service_email' => $service->email,
            ]);

            return redirect()->route('services.compte')->with('success', 'Connexion réussie');
        }

        // Sinon erreur
        return back()->withErrors(['email' => 'Email ou mot de passe incorrect']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('services.login')->with('success', 'Déconnecté');
    }
}
