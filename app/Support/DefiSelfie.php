<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Défi aléatoire pour le selfie : un code à 4 chiffres à écrire sur une feuille + un geste.
 * Le défi est lié à la session, valable 10 minutes, et ne sert qu'une fois.
 * Une photo prise à l'avance ou volée ne peut pas contenir un code qui n'existait pas encore.
 */
class DefiSelfie
{
    public const DUREE_MINUTES = 10;

    private const GESTES = [
        'levez la main droite, paume ouverte',
        'montrez trois doigts',
        'montrez deux doigts en V',
        'pointez le pouce vers le haut',
        'touchez votre nez avec un doigt',
        'tournez la tête vers la gauche',
        'tournez la tête vers la droite',
        'fermez l\'œil gauche',
    ];

    /** Crée un nouveau défi (remplace le précédent) et le renvoie au navigateur. */
    public static function generer(): array
    {
        $code  = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $geste = self::GESTES[array_rand(self::GESTES)];

        $defi = [
            'id'     => Str::random(32),
            'texte'  => "Écrivez le code $code sur une feuille, tenez-la près de votre visage et $geste.",
            'expire' => now()->addMinutes(self::DUREE_MINUTES)->timestamp,
        ];

        session(['defi_selfie' => $defi]);

        return ['id' => $defi['id'], 'texte' => $defi['texte'], 'duree' => self::DUREE_MINUTES * 60];
    }

    /** Vrai si l'identifiant correspond au défi en cours, non expiré. */
    public static function valide(?string $id): bool
    {
        $d = session('defi_selfie');

        return is_array($d) && $id && hash_equals($d['id'], $id) && $d['expire'] >= time();
    }

    /** Vérifie puis consomme le défi. Renvoie sa consigne, ou null s'il est invalide ou expiré. */
    public static function consommer(?string $id): ?string
    {
        if (! self::valide($id)) {
            return null;
        }

        $texte = session('defi_selfie')['texte'];
        session()->forget('defi_selfie');

        return $texte;
    }

    public const MESSAGE_EXPIRE = 'Le défi du selfie a expiré ou est invalide. Reprenez le selfie en suivant la nouvelle consigne.';
}
