<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Support\DefiSelfie;
use App\Support\Identite;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CitoyenController extends Controller
{
    public function store(Request $request)
    {
        // Un dossier rejeté ne bloque pas son email : la personne peut se réinscrire (le même dossier est réutilisé).
        $rejete = Citoyens::where('email', $request->input('email'))
            ->where('statut_verification', 'rejete')
            ->first();

        $majeur = now()->subYears(18)->toDateString();

        // Validation (10 Mo : une photo de téléphone dépasse presque toujours les 2 Mo d'avant)
        $validated = $request->validate([
            'nom'                   => 'required|string|max:255',
            'prenom'                => 'required|string|max:255',
            'sexe'                  => 'required|in:M,F',
            'date_naissance'        => 'required|date|after:1900-01-01|before_or_equal:' . $majeur,
            'adresse'               => 'required|string|max:255',
            'telephone'             => 'required|string|max:20',
            'email'                 => ['required', 'email', Rule::unique('citoyens', 'email')->ignore($rejete?->id)],
            'password'              => 'required|confirmed|min:6',
            'type_piece'            => ['required', Rule::in(array_keys(Identite::TYPES))],
            'numero_piece'          => ['required', 'string', 'min:5', 'max:40', 'regex:/^[A-Za-z0-9\s\-\/]+$/'],
            'date_expiration_piece' => 'required|date|after:today',
            'piece_identite'        => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'selfie'                => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'defi_id'               => 'required|string|size:32',
            'selfie_direct'         => 'nullable|boolean',
        ], array_merge(ImageUpload::MESSAGES, [
            'password.min'                  => 'Le mot de passe doit contenir au minimum 6 caractères.',
            'password.confirmed'            => 'La confirmation du mot de passe est incorrecte.',
            'email.unique'                  => 'Cet email est déjà utilisé.',
            'date_naissance.before_or_equal' => 'Vous devez avoir au moins 18 ans pour vous inscrire.',
            'date_expiration_piece.after'   => "Votre pièce d'identité est expirée : utilisez une pièce en cours de validité.",
            'numero_piece.regex'            => 'Le numéro de pièce ne doit contenir que des lettres, des chiffres, des espaces ou des tirets.',
            'selfie.required'               => 'Le selfie est obligatoire.',
            'selfie.image'                  => 'Le selfie doit être une image valide.',
            'selfie.mimes'                  => 'Formats acceptés : JPG, PNG ou WebP.',
            'selfie.max'                    => 'Le selfie ne doit pas dépasser 10 Mo.',
            'selfie.uploaded'               => "Le selfie n'a pas pu être envoyé (fichier trop lourd ou connexion coupée). Réessayez.",
        ]));

        // Défi du selfie : la consigne (code + geste) doit exister, être récente, et ne sert qu'une fois.
        $defi = DefiSelfie::consommer($validated['defi_id']);
        if ($defi === null) {
            throw ValidationException::withMessages(['selfie' => DefiSelfie::MESSAGE_EXPIRE]);
        }

        // Un même numéro de pièce ne peut servir qu'à un seul compte.
        $empreinte = Identite::empreinte($validated['type_piece'], $validated['numero_piece']);
        if (Citoyens::where('numero_piece_hash', $empreinte)
                ->when($rejete, fn ($q) => $q->where('id', '!=', $rejete->id))
                ->exists()) {
            throw ValidationException::withMessages([
                'numero_piece' => "Cette pièce d'identité est déjà associée à un compte. Si c'est la vôtre, connectez-vous ou contactez-nous.",
            ]);
        }

        // Pièce et selfie : réduits, chiffrés, puis envoyés. Si un envoi échoue, l'inscription est refusée avec un message.
        $cheminPiece = Identite::stockerChiffre($request->file('piece_identite'), 'pieces_identite', 'piece_identite');
        try {
            $cheminSelfie = Identite::stockerChiffre($request->file('selfie'), 'selfies_identite', 'selfie');
        } catch (\Throwable $e) {
            ImageUpload::supprimer($cheminPiece, 'pieces_identite'); // pas de fichier orphelin
            throw $e;
        }

        $donnees = [
            'nom'                   => $validated['nom'],
            'prenom'                => $validated['prenom'],
            'sexe'                  => $validated['sexe'],
            'date_naissance'        => $validated['date_naissance'],
            'adresse'               => $validated['adresse'],
            'telephone'             => $validated['telephone'],
            'email'                 => $validated['email'],
            'password'              => Hash::make($validated['password']),
            'matricule'             => 'CIT-' . strtoupper(Str::random(10)),
            'etat_compte'           => 'desactif',
            'type_piece'            => $validated['type_piece'],
            'numero_piece'          => Crypt::encryptString(Identite::normaliserNumero($validated['numero_piece'])),
            'numero_piece_hash'     => $empreinte,
            'date_expiration_piece' => $validated['date_expiration_piece'],
            'piece_identite'        => $cheminPiece,
            'selfie_identite'       => $cheminSelfie,
            'statut_verification'   => 'en_attente',
            'defi_selfie'           => $defi,
            'selfie_pris_le'        => now(),
            'selfie_direct'         => $request->boolean('selfie_direct'),
        ];

        if ($rejete) {
            // Nouvelle demande après rejet : on remet le même dossier à zéro avec les nouvelles données.
            $anciennePiece = $rejete->piece_identite;
            $ancienSelfie  = $rejete->selfie_identite;

            unset($donnees['matricule']); // le matricule d'origine est conservé

            $rejete->forceFill($donnees + [
                'motif_rejet'      => null,
                'verifie_par'      => null,
                'verifie_le'       => null,
                'email_verifie_le' => null,
                'created_at'       => now(), // le dossier repasse en bas de la file, comme une nouvelle demande
            ])->save();

            $citoyen = $rejete;

            if ($anciennePiece) { ImageUpload::supprimer($anciennePiece, 'pieces_identite'); }
            if ($ancienSelfie)  { ImageUpload::supprimer($ancienSelfie, 'selfies_identite'); }

            Identite::journaliser($citoyen->id, 'renvoi_dossier', 'Nouvelle inscription après rejet');
        } else {
            $citoyen = Citoyens::create($donnees);
            Identite::journaliser($citoyen->id, 'inscription', 'Dossier déposé par le citoyen');
        }

        // Mail : confirmation de l'email (lien signé, valable 3 jours) + information sur la vérification
        try {
            $lien = URL::temporarySignedRoute('citoyen.email.verifier', now()->addDays(3), ['id' => $citoyen->id]);

            Mail::send([], [], function ($message) use ($citoyen, $lien) {
                $message->to($citoyen->email)
                    ->subject('Confirmez votre email - CongoAssist')
                    ->html("
                        <p>Bonjour <strong>{$citoyen->prenom}</strong>,</p>
                        <p>Votre compte a été créé sur <strong>CongoAssist</strong>.</p>
                        <p>1. Confirmez votre adresse email : <a href=\"{$lien}\">cliquez ici</a> (lien valable 3 jours).</p>
                        <p>2. Notre équipe contrôle votre pièce d'identité. Votre compte sera
                        <strong>activé dans un délai de 24 heures</strong> si le dossier est conforme.
                        Sinon, vous recevrez un email précisant ce qu'il faut corriger.</p>
                        <p>Cordialement,<br><strong>L'équipe CongoAssist</strong></p>
                    ");
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi mail citoyen : ' . $e->getMessage());
        }

        return redirect()
            ->route('login')
            ->with('success', "Bienvenue {$citoyen->prenom} ! Votre dossier a bien été reçu : il sera contrôlé et votre compte activé sous 24 heures. Pensez à confirmer votre email (lien envoyé par mail). En cas de danger immédiat, n'attendez pas : l'alerte de la page d'accueil fonctionne sans compte.");
    }

    /** Génère une consigne de selfie aléatoire (code + geste), valable 10 minutes. */
    public function defiSelfie()
    {
        return response()->json(DefiSelfie::generer())->header('Cache-Control', 'no-store');
    }

    /** Lien signé reçu par mail : confirme que l'adresse email appartient bien à la personne. */
    public function verifierEmail(Request $request, $id)
    {
        $citoyen = Citoyens::findOrFail($id);

        if (! $citoyen->email_verifie_le) {
            $citoyen->update(['email_verifie_le' => now()]);
            Identite::journaliser($citoyen->id, 'email_verifie', null);
        }

        return redirect()->route('login')->with('success', 'Votre adresse email est confirmée. Merci !');
    }
}
