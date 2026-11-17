<?php

namespace App\Support;

use App\Models\Citoyens;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/** Outils communs à la vérification d'identité des citoyens. */
class Identite
{
    public const TYPES = [
        'cni'       => "Carte nationale d'identité",
        'passeport' => 'Passeport',
        'permis'    => 'Permis de conduire',
    ];

    /** Cases que l'admin doit toutes cocher avant de valider. */
    public const CONTROLES = [
        'photo_nette'    => 'La pièce est lisible et la photo est nette',
        'nom_concordant' => "Le nom et le prénom correspondent à l'inscription",
        'naissance'      => 'La date de naissance correspond',
        'non_expiree'    => "La pièce n'est pas expirée",
        'selfie'         => 'Le selfie correspond à la photo de la pièce',
        'defi'           => 'Le code et le geste demandés sont bien visibles sur le selfie',
    ];

    public static function normaliserNumero(string $numero): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $numero));
    }

    /** Empreinte non réversible du numéro (sert à refuser un deuxième compte sur la même pièce). */
    public static function empreinte(string $type, string $numero): string
    {
        return hash_hmac('sha256', $type . '|' . self::normaliserNumero($numero), (string) config('app.key'));
    }

    /** Écrit une ligne dans le journal. Ne bloque jamais l'action principale. */
    public static function journaliser(int $citoyenId, string $action, ?string $detail = null): void
    {
        try {
            DB::table('verification_journal')->insert([
                'citoyen_id' => $citoyenId,
                'admin_id'   => session('admin_id'),
                'action'     => $action,
                'detail'     => $detail,
                'ip'         => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Journal de vérification non enregistré', ['erreur' => $e->getMessage()]);
        }
    }

    /** Contrôles exigés : les anciens comptes (sans selfie, sans dates) ont une liste plus courte. */
    public static function controlesPour(Citoyens $citoyen): array
    {
        $liste = self::CONTROLES;

        if (! $citoyen->date_naissance)        { unset($liste['naissance']); }
        if (! $citoyen->date_expiration_piece) { unset($liste['non_expiree']); }
        if (! $citoyen->selfie_identite)       { unset($liste['selfie']); }
        if (! $citoyen->defi_selfie)           { unset($liste['defi']); }

        return $liste;
    }

    /** Réduit, chiffre puis envoie une image. Renvoie le chemin enregistré en base. */
    public static function stockerChiffre(UploadedFile $fichier, string $dossier, string $champ): string
    {
        [$contenu] = ImageUpload::reduire($fichier, 1600);

        $chemin = $dossier . '/' . time() . '_' . Str::uuid() . '.enc';
        ImageUpload::envoyerChemin($chemin, Crypt::encrypt($contenu), $champ);

        return $chemin;
    }
}
