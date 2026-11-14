<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Envoi d'images / médias vers Supabase Storage, avec :
 *  - réduction automatique des photos de téléphone (4000 px / 5 Mo -> 1280 px / ~300 Ko) ;
 *  - vérification réelle que le fichier est bien parti (le disque « supabase » n'a pas
 *    "throw" => true : avant, un échec d'envoi était ignoré et une URL cassée était enregistrée) ;
 *  - URL publique fiable, même si SUPABASE_PUBLIC_URL n'est pas renseigné.
 */
class ImageUpload
{
    /** Photo de profil : 10 Mo (les photos Android dépassent souvent les 2 Mo d'avant). */
    public const REGLE_IMAGE = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'];

    /** Média d'actualité (image ou vidéo). */
    public const REGLE_MEDIA = ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4', 'max:51200'];

    public const MESSAGES = [
        'photo.uploaded'        => "La photo n'a pas pu être envoyée (fichier trop lourd ou connexion coupée). Réessayez.",
        'photo.image'           => 'Le fichier doit être une image valide.',
        'photo.mimes'           => 'Formats acceptés : JPG, PNG ou WebP.',
        'photo.max'             => 'La photo ne doit pas dépasser 10 Mo.',
        'photo_profil.uploaded' => "La photo n'a pas pu être envoyée (fichier trop lourd ou connexion coupée). Réessayez.",
        'photo_profil.image'    => 'Le fichier doit être une image valide.',
        'photo_profil.mimes'    => 'Formats acceptés : JPG, PNG ou WebP.',
        'photo_profil.max'      => 'La photo ne doit pas dépasser 10 Mo.',
        'piece_identite.uploaded' => "La pièce d'identité n'a pas pu être envoyée (fichier trop lourd ou connexion coupée). Réessayez.",
        'piece_identite.image'  => "La pièce d'identité doit être une image valide.",
        'piece_identite.mimes'  => 'Formats acceptés : JPG, PNG ou WebP.',
        'piece_identite.max'    => "La pièce d'identité ne doit pas dépasser 10 Mo.",
        'url_media.uploaded'    => "Le média n'a pas pu être envoyé (fichier trop lourd ou connexion coupée). Réessayez.",
        'url_media.mimes'       => 'Formats acceptés : JPG, PNG, WebP, GIF ou MP4.',
        'url_media.max'         => 'Le média ne doit pas dépasser 50 Mo.',
    ];

    /* ───────────── URL publique ───────────── */

    public static function base(): string
    {
        $base = rtrim((string) config('filesystems.disks.supabase.public_url'), '/');

        if ($base === '') {
            // Déduit l'URL publique de l'endpoint S3 de Supabase :
            // https://xxxx.supabase.co/storage/v1/s3  ->  https://xxxx.supabase.co/storage/v1/object/public/<bucket>
            $endpoint = rtrim((string) config('filesystems.disks.supabase.endpoint'), '/');
            $bucket   = (string) config('filesystems.disks.supabase.bucket');
            if ($endpoint !== '' && $bucket !== '') {
                $base = preg_replace('#/storage/v1/s3$#', '/storage/v1/object/public', $endpoint) . '/' . $bucket;
            }
        }

        return $base;
    }

    public static function url(string $chemin): string
    {
        return self::base() . '/' . ltrim($chemin, '/');
    }

    /** Retrouve le chemin dans le bucket à partir d'une URL enregistrée en base. */
    public static function chemin(?string $url, string $dossierParDefaut = ''): ?string
    {
        if (! $url) {
            return null;
        }

        $base = self::base();
        if ($base !== '' && str_starts_with($url, $base . '/')) {
            return substr($url, strlen($base) + 1);
        }

        $nom = basename((string) parse_url($url, PHP_URL_PATH));

        return $nom === '' ? null : trim($dossierParDefaut . '/' . $nom, '/');
    }

    public static function supprimer(?string $url, string $dossierParDefaut = ''): void
    {
        $chemin = self::chemin($url, $dossierParDefaut);
        if (! $chemin) {
            return;
        }

        try {
            Storage::disk('supabase')->delete($chemin);
        } catch (\Throwable $e) {
            report($e); // une ancienne image impossible à supprimer ne doit pas bloquer la mise à jour
        }
    }

    /* ───────────── Envoi ───────────── */

    /** Réduit puis envoie une image. Renvoie l'URL publique. */
    public static function storeImage(UploadedFile $fichier, string $dossier, string $champ = 'photo', int $cote = 1280): string
    {
        [$contenu, $ext] = self::reduire($fichier, $cote);

        return self::envoyerContenu($dossier, $ext, $contenu, $champ);
    }

    /** Média d'actualité. Renvoie [url, type] avec type = 'image' | 'mp4'. */
    public static function storeMedia(UploadedFile $fichier, string $dossier, string $champ = 'url_media'): array
    {
        $ext = strtolower($fichier->extension() ?: $fichier->getClientOriginalExtension());

        if ($ext === 'mp4' || $ext === 'gif') {
            // Vidéo et GIF animé : envoyés tels quels (en flux, sans tout charger en mémoire).
            $nom = Str::uuid() . '.' . $ext;
            $ok  = false;
            try {
                $ok = (bool) Storage::disk('supabase')->putFileAs(trim($dossier, '/'), $fichier, $nom);
            } catch (\Throwable $e) {
                report($e);
            }
            if (! $ok) {
                throw ValidationException::withMessages([$champ => "Impossible d'envoyer le média pour le moment. Réessayez dans un instant."]);
            }

            return [self::url(trim($dossier, '/') . '/' . $nom), $ext === 'mp4' ? 'mp4' : 'image'];
        }

        return [self::storeImage($fichier, $dossier, $champ, 1600), 'image'];
    }

    /** Envoie un contenu déjà en mémoire (utilisé aussi pour la pièce d'identité chiffrée). */
    public static function envoyerContenu(string $dossier, string $ext, string $contenu, string $champ = 'photo'): string
    {
        $chemin = trim($dossier, '/') . '/' . Str::uuid() . '.' . $ext;

        return self::envoyerChemin($chemin, $contenu, $champ);
    }

    /** Envoi vers un chemin précis ; renvoie l'URL publique. Échoue (erreur de formulaire) si l'envoi n'a pas eu lieu. */
    public static function envoyerChemin(string $chemin, string $contenu, string $champ = 'photo'): string
    {
        $ok = false;

        try {
            $ok = (bool) Storage::disk('supabase')->put($chemin, $contenu);
        } catch (\Throwable $e) {
            report($e);
        }

        if (! $ok) {
            throw ValidationException::withMessages([$champ => "Impossible d'enregistrer le fichier pour le moment. Réessayez dans un instant."]);
        }

        return self::url($chemin);
    }

    /* ───────────── Réduction (GD) ───────────── */

    /** @return array{0:string,1:string} [contenu binaire, extension] */
    public static function reduire(UploadedFile $fichier, int $cote = 1280): array
    {
        $ext = strtolower($fichier->extension() ?: $fichier->getClientOriginalExtension());
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $brut = [file_get_contents($fichier->getRealPath()), $ext];

        if (! function_exists('imagecreatefromstring')) {
            return $brut;
        }

        try {
            $info = @getimagesize($fichier->getRealPath());
            if (! $info) {
                return $brut;
            }

            [$l, $h] = $info;

            // Déjà léger : on ne touche pas.
            if ($fichier->getSize() < 600 * 1024 && max($l, $h) <= $cote) {
                return $brut;
            }

            // Garde-fou mémoire : le décodage coûte ~4 octets par pixel (+ copie réduite).
            $limite = self::octets(ini_get('memory_limit'));
            if ($limite > 0 && ($l * $h * 6) > ($limite - memory_get_usage(true))) {
                return $brut;
            }

            $src = @imagecreatefromstring($brut[0]);
            if (! $src) {
                return $brut; // GD sans support de ce format : envoi du fichier d'origine
            }

            // Orientation EXIF (photos prises à la verticale sur Android / iPhone)
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $exif  = @exif_read_data($fichier->getRealPath());
                $angle = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
                if ($angle) {
                    $pivot = imagerotate($src, $angle, 0);
                    if ($pivot) {
                        imagedestroy($src);
                        $src = $pivot;
                    }
                }
            }

            $l = imagesx($src);
            $h = imagesy($src);
            $ratio = min(1, $cote / max($l, $h));
            $nl = max(1, (int) round($l * $ratio));
            $nh = max(1, (int) round($h * $ratio));

            $dst = imagecreatetruecolor($nl, $nh);
            $png = $info[2] === IMAGETYPE_PNG;

            if ($png) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
            } else {
                imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nl, $nh, $l, $h);

            ob_start();
            $png ? imagepng($dst, null, 6) : imagejpeg($dst, null, 82);
            $sortie = ob_get_clean();

            imagedestroy($src);
            imagedestroy($dst);

            if ($sortie === false || $sortie === '') {
                return $brut;
            }

            return [$sortie, $png ? 'png' : 'jpg'];
        } catch (\Throwable $e) {
            return $brut;
        }
    }

    private static function octets($valeur): int
    {
        $valeur = trim((string) $valeur);
        if ($valeur === '' || $valeur === '-1') {
            return 0;
        }
        $n = (int) $valeur;

        return match (strtolower(substr($valeur, -1))) {
            'g' => $n * 1024 ** 3,
            'm' => $n * 1024 ** 2,
            'k' => $n * 1024,
            default => $n,
        };
    }
}
