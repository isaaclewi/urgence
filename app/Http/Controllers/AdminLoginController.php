<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;


class AdminLoginController extends Controller
{
    // Affiche le formulaire login
    public function showLoginForm()
    {
        return view('admin.login'); // fichier blade
    }

    // Traitement login
    public function processLogin(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'mot_passe' => 'required',
        ]);

// Récupérer l'admin par email seulement
$admin = Admins::where('email', $request->email)->first();

if ($admin && Hash::check($request->mot_passe, $admin->mot_passe)) {
    // mot de passe correct
    session([
        'admin_id' => $admin->id,
        'admin_nom' => $admin->nom,
        'admin_photo' => $admin->photo_profil,
        'admin_sexe' => $admin->sexe,
        'admin_adresse' => $admin->adresse,
        'admin_telephone' => $admin->telephone,
        'admin_email' => $admin->email,
    ]);

    return redirect()->route('admin.compte')->with('success', 'Connexion réussie');
}

// Sinon erreur
return back()->withErrors(['email' => 'Email ou mot de passe incorrect']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('admin.login')->with('success', 'Déconnecté');
    }
}
