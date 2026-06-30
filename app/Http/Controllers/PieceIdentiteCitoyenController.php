<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class PieceIdentiteCitoyenController extends Controller
{
    //
    public function show($id)
    {
        //Récupération du le citoyen
        $citoyen = Citoyens::findOrFail($id);

        // Vérification de la pièce d'identité
        if (!$citoyen->piece_identite) {
            abort(404, "Pièce d'identité introuvable");
        }

        // Lecture du fichier chiffré depuis Supabase
        $contenuChiffre = Storage::disk('supabase')
            ->get($citoyen->piece_identite);

        // Déchiffrement
        $contenu = Crypt::decrypt($contenuChiffre);

        // Retourner l'image au navigateur
        return response($contenu)
            ->header('Content-Type', 'image/jpeg');
    }
}
