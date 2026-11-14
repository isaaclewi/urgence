<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Support\ImageUpload;

class CitoyenController extends Controller
{
    public function store(Request $request)
    {
        // Validation (10 Mo : une photo de téléphone dépasse presque toujours les 2 Mo d'avant)
        $validated = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'sexe'           => 'required|in:M,F',
            'adresse'        => 'required|string|max:255',
            'telephone'      => 'required|string|max:20',
            'email'          => 'required|email|unique:citoyens,email',
            'password'       => 'required|confirmed|min:6',
            'piece_identite' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], array_merge(ImageUpload::MESSAGES, [
            'password.min'       => 'Le mot de passe doit contenir au minimum 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe est incorrecte.',
            'email.unique'       => 'Cet email est déjà utilisé.',
        ]));

        // Pièce d'identité : réduite, chiffrée, puis envoyée. Si l'envoi échoue, l'inscription
        // est refusée avec un message (avant : le chemin était enregistré même sans fichier).
        [$contenu] = ImageUpload::reduire($request->file('piece_identite'), 1600);

        $fichierChiffre = time() . '_' . Str::uuid() . '.enc';
        ImageUpload::envoyerChemin(
            'pieces_identite/' . $fichierChiffre,
            Crypt::encrypt($contenu),
            'piece_identite'
        );

        $validated['piece_identite'] = 'pieces_identite/' . $fichierChiffre;

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

        // Le compte n'est pas encore actif : on ne connecte pas la personne (avant, elle était
        // connectée puis aussitôt renvoyée avec « Votre compte est inactif », sans voir le message de bienvenue).
        return redirect()
            ->route('login')
            ->with('success', "Bienvenue {$citoyen->prenom} ! Votre compte a bien été créé et sera activé dans 24 heures. Vous pourrez alors vous connecter.");
    }
}
