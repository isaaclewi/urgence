<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Affiche le formulaire login
    public function showLoginForm()
    {
        return view('login'); // fichier blade
    }

    // Traitement login
    public function processLogin(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Vérifier si le citoyen existe
        $citoyen = Citoyens::where('email', $request->email)->first();

        if ($citoyen && Hash::check($request->password, $citoyen->password)) {
            // Mettre l'ID en session
            session([
                'citoyen_id' => $citoyen->id,
                'citoyen_nom' => $citoyen->nom,
                'citoyen_photo' => $citoyen->piece_identite,
                'citoyen_sexe' => $citoyen->sexe,
                'citoyen_adresse' => $citoyen->adresse,
                'citoyen_telephone' => $citoyen->telephone,
                'citoyen_email' => $citoyen->email,
            ]);

            return redirect()->route('compteController')->with('success', 'Connexion réussie');
        }

        // Sinon erreur
        return back()->withErrors(['email' => 'Email ou mot de passe incorrect']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('accueil')->with('success', 'Déconnecté');
    }
}
