<?php

// dans App\Http\Controllers\bilanController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function index()
    {
        // Vérifier si un citoyen est connecté
        if (session()->has('citoyen_id')) {
            $citoyen = Citoyens::find(session('citoyen_id'));

            // Récupérer le bilan du citoyen (dernier bilan par exemple)
            $bilan = bilanSante::where('citoyen_id', $citoyen->id)->latest()->first();

             // Déchiffrement des champs sensibles pour l'affichage
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

            return view('bilanSante', compact('citoyen', 'bilan'));
        }

        // Si aucun citoyen en session, rediriger vers login
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }
}

