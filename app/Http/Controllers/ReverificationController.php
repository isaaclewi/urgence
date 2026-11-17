<?php

namespace App\Http\Controllers;

use App\Models\Citoyens;
use App\Support\Identite;
use App\Support\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Le citoyen dont le dossier a été rejeté renvoie sa pièce et son selfie
 * depuis le lien signé reçu par mail (il ne peut pas se connecter : son compte n'est pas actif).
 */
class ReverificationController extends Controller
{
    public function form(Request $request, $id)
    {
        $citoyen = $this->citoyenARevoir($id);

        return view('reverification', [
            'citoyen' => $citoyen,
            'action'  => URL::temporarySignedRoute('citoyen.reverif.soumettre', now()->addDays(7), ['id' => $citoyen->id]),
        ]);
    }

    public function soumettre(Request $request, $id)
    {
        $citoyen = $this->citoyenARevoir($id);

        $validated = $request->validate([
            'date_naissance'        => 'required|date|after:1900-01-01|before_or_equal:' . now()->subYears(18)->toDateString(),
            'type_piece'            => ['required', Rule::in(array_keys(Identite::TYPES))],
            'numero_piece'          => ['required', 'string', 'min:5', 'max:40', 'regex:/^[A-Za-z0-9\s\-\/]+$/'],
            'date_expiration_piece' => 'required|date|after:today',
            'piece_identite'        => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'selfie'                => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], array_merge(ImageUpload::MESSAGES, [
            'date_naissance.before_or_equal' => 'Vous devez avoir au moins 18 ans.',
            'date_expiration_piece.after'    => "Votre pièce d'identité est expirée : utilisez une pièce en cours de validité.",
            'numero_piece.regex'             => 'Le numéro de pièce ne doit contenir que des lettres, des chiffres, des espaces ou des tirets.',
            'selfie.required'                => 'Le selfie est obligatoire.',
            'selfie.image'                   => 'Le selfie doit être une image valide.',
        ]));

        $empreinte = Identite::empreinte($validated['type_piece'], $validated['numero_piece']);
        if (Citoyens::where('numero_piece_hash', $empreinte)->where('id', '!=', $citoyen->id)->exists()) {
            throw ValidationException::withMessages([
                'numero_piece' => "Cette pièce d'identité est déjà associée à un autre compte.",
            ]);
        }

        $anciennePiece  = $citoyen->piece_identite;
        $ancienSelfie   = $citoyen->selfie_identite;

        $cheminPiece  = Identite::stockerChiffre($request->file('piece_identite'), 'pieces_identite', 'piece_identite');
        try {
            $cheminSelfie = Identite::stockerChiffre($request->file('selfie'), 'selfies_identite', 'selfie');
        } catch (\Throwable $e) {
            ImageUpload::supprimer($cheminPiece, 'pieces_identite');
            throw $e;
        }

        $citoyen->update([
            'date_naissance'        => $validated['date_naissance'],
            'type_piece'            => $validated['type_piece'],
            'numero_piece'          => Crypt::encryptString(Identite::normaliserNumero($validated['numero_piece'])),
            'numero_piece_hash'     => $empreinte,
            'date_expiration_piece' => $validated['date_expiration_piece'],
            'piece_identite'        => $cheminPiece,
            'selfie_identite'       => $cheminSelfie,
            'statut_verification'   => 'en_attente',
            'motif_rejet'           => null,
        ]);

        // Les anciens fichiers ne servent plus.
        if ($anciennePiece) { ImageUpload::supprimer($anciennePiece, 'pieces_identite'); }
        if ($ancienSelfie)  { ImageUpload::supprimer($ancienSelfie, 'selfies_identite'); }

        Identite::journaliser($citoyen->id, 'renvoi_dossier', 'Nouveau dossier déposé après rejet');

        return redirect()->route('login')->with('success', 'Merci ! Votre nouveau dossier a été reçu. Il sera contrôlé sous 24 heures.');
    }

    private function citoyenARevoir($id): Citoyens
    {
        $citoyen = Citoyens::findOrFail($id);

        abort_unless($citoyen->statut_verification === 'rejete', 403, "Ce dossier n'est pas à corriger.");

        return $citoyen;
    }
}
