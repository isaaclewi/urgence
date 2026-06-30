<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class CitoyenController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'sexe'           => 'required|in:M,F',
            'adresse'        => 'required|string|max:255',
            'telephone'      => 'required|string|max:20',
            'email'          => 'required|email|unique:citoyens,email',
            'password'       => 'required|confirmed|min:6',
            'piece_identite' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'password.min'          => 'Le mot de passe doit contenir au minimum 6 caractères.',
            'password.confirmed'    => 'La confirmation du mot de passe est incorrecte.',
            'piece_identite.max'    => 'La taille de l\'image ne doit pas dépasser 2 Mo.',
            'piece_identite.image'  => 'La pièce jointe doit être une image valide.',
            'email.unique'          => 'Cet email est déjà utilisé.',
        ]);

        // Upload pièce d'identité → Supabase Storage
        if ($request->hasFile('piece_identite')) {
            $file     = $request->file('piece_identite');

            //Lecture du contenu du fichier binaire
            $contenu = file_get_contents($file->getRealPath());

            //Chriffrement du contenu
            $contenuChiffre = Crypt::encrypt($contenu);

            //Fichier chiffré 
            $fichierChiffre = time() . '_' . Str::uuid() . '.enc';

            //Stockage en local du fichier chiffré
            Storage::disk('supabase')->put(
                'pieces_identite/' . $fichierChiffre,
                $contenuChiffre
            );

            //Sauvegarde en base de données du chemin du fichier chiffré
            $validated['piece_identite'] = 'pieces_identite/' . $fichierChiffre;
        }

        // Matricule unique
        $validated['matricule'] = 'CIT-' . strtoupper(Str::random(10));

        // Mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // État par défaut
        $validated['etat_compte'] = 'desactif';

        // Création
        $citoyen = Citoyens::create($validated);

        // Envoi email
        try {
            Mail::send([], [], function ($message) use ($citoyen) {
                $message->to($citoyen->email)
                    ->subject('Activation de votre compte CongoAssist')
                    ->html("
                        <p>Bonjour <strong>{$citoyen->prenom}</strong>,</p>
                        <p>Votre compte a été créé avec succès sur <strong>CongoAssist</strong>.</p>
                        <p>⏳ Votre compte est actuellement <strong>désactivé</strong> et sera
                        <strong>activé dans un délai de 24 heures</strong> après vérification.</p>
                        <p>Vous recevrez une notification dès l'activation de votre compte.</p>
                        <p>Cordialement,<br><strong>L'équipe CongoAssist</strong></p>
                    ");
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi mail citoyen : ' . $e->getMessage());
        }

        // Session
        session([
            'citoyen_id'        => $citoyen->id,
            'citoyen_nom'       => $citoyen->nom,
            'citoyen_prenom'    => $citoyen->prenom,
            'citoyen_etat'      => $citoyen->etat_compte,
            'citoyen_matricule' => $citoyen->matricule,
        ]);

        return redirect()
            ->route('compte')
            ->with('success', "Bienvenue {$citoyen->prenom}, votre compte sera activé dans 24 heures.");
    }
}
