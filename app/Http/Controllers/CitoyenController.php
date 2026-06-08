<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citoyens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


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
    'password.min' => 'Le mot de passe doit contenir au minimum 6 caractères.',
    'password.confirmed' => 'La confirmation du mot de passe est incorrecte.',
    'piece_identite.max' => 'La taille de l’image ne doit pas dépasser 2 Mo.',
    'piece_identite.image' => 'La pièce jointe doit être une image valide.',
    'email.unique' => 'Cet email est déjà utilisé.',
]);


        // Upload pièce d'identité
if ($request->hasFile('piece_identite')) {
    $file = $request->file('piece_identite');
    $filename = time() . '_' .
        Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
        '.' . $file->getClientOriginalExtension();

    // Utiliser storage/app/public au lieu de public/uploads
    $path = $file->storeAs('pieces', $filename, 'public');
    $validated['piece_identite'] = 'storage/' . $path;
}

        // Matricule unique
        $validated['matricule'] = 'CIT-' . strtoupper(Str::random(10));

        // Mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // État par défaut
        $validated['etat_compte'] = 'desactif';

        // Création
        $citoyen = Citoyens::create($validated);

        // Envoi email SMTP au citoyen
try {
    Mail::send([], [], function ($message) use ($citoyen) {
        $message->to($citoyen->email)
            ->subject('Activation de votre compte CongoAssist')
            ->html("
                <p>Bonjour <strong>{$citoyen->prenom}</strong>,</p>

                <p>
                    Votre compte a été créé avec succès sur <strong>CongoAssist</strong>.
                </p>

                <p>
                    ⏳ Votre compte est actuellement <strong>désactivé</strong> et sera
                    <strong>activé dans un délai de 24 heures</strong> après vérification.
                </p>

                <p>
                    Vous recevrez une notification dès l’activation de votre compte.
                </p>

                <p>
                    Cordialement,<br>
                    <strong>L’équipe CongoAssist</strong>
                </p>
            ");
    });
} catch (\Exception $e) {
    // Optionnel : log si l’email échoue (ne bloque pas l’inscription)
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

        // Message propre (pas de echo)
        return redirect()
            ->route('compteController')
            ->with('success', "Bienvenue {$citoyen->prenom}, votre compte sera activé dans 24 heures.");
    }
}
