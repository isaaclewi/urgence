<?php

// dans App\Http\Controllers\bilanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Citoyens;
use App\Models\bilanSante;

class bilanController extends Controller
{
    //Tableau regorgeant les champs chiffrés en  base de données
    private array $champsChiffres = [
        'allergies',
        'groupe_sanguin',
        'taille',
        'poids',
        'antecedents_familiaux',
        'mode_de_vie',
        'maladies_chroniques',
        'maladies_passees_importantes',
        'interventions_chirurgicales',
        'antecedents_hospitalisation',
        'medicaments_pris_actuellement',
    ];

    /** Durée (minutes) pendant laquelle le bilan reste visible après saisie du mot de passe. */
    private const DUREE_DEVERROUILLAGE = 10;

    private function estDeverrouille(): bool
    {
        return (int) session('bilan_deverrouille_jusqua', 0) > now()->timestamp;
    }

    /** Vérifie le mot de passe du compte avant d'afficher le dossier médical. */
    public function deverrouiller(Request $request)
    {
        $citoyen = Citoyens::find(session('citoyen_id'));

        if (! $citoyen) {
            return redirect()->route('login')->with('error', 'Citoyen introuvable.');
        }

        $request->validate(
            ['password' => 'required|string'],
            ['password.required' => 'Saisissez votre mot de passe pour afficher votre bilan.']
        );

        if (! Hash::check($request->password, $citoyen->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        session(['bilan_deverrouille_jusqua' => now()->addMinutes(self::DUREE_DEVERROUILLAGE)->timestamp]);

        return redirect()->route('bilanController');
    }

    public function verrouiller()
    {
        session()->forget('bilan_deverrouille_jusqua');

        return redirect()->route('bilanController');
    }

    public function index()
    {
        // Vérifier si un citoyen est connecté
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));

            if (! $citoyen) {
                session()->forget('citoyen_id');
                return redirect()->route('login')->with('error', 'Citoyen introuvable.');
            }

            // Dossier verrouillé : on n'envoie aucune donnée médicale à la page
            if (! $this->estDeverrouille()) {
                return view('bilanSante', ['citoyen' => $citoyen, 'bilan' => null, 'verrouille' => true]);
            }

            // Récupérer le bilan du citoyen (dernier bilan par exemple)
            $bilan = bilanSante::where('citoyen_id', $citoyen->id)->latest()->first();

            // Déchiffrement des champs sensibles pour la lecture
            if ($bilan) {
                foreach ($this->champsChiffres as $champ) {
                    if (!empty($bilan->$champ)) {
                        try {
                            $bilan->$champ = decrypt($bilan->$champ);
                        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                            $bilan->$champ = 'Erreur de déchiffrement';
                        }
                    }
                }
            }

            $verrouille = false;

            return view('bilanSante', compact('citoyen', 'bilan', 'verrouille'));
        }

        // Si aucun citoyen en session, rediriger vers login
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }
}
