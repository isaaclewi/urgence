<?php

namespace App\Models\Concerns;

/**
 * Garantit que $modele->photo_profil est toujours une chaîne (URL) ou null,
 * même si la colonne est encore binaire (PostgreSQL renvoie alors une « ressource »).
 */
trait NormalisePhotoProfil
{
    public function getPhotoProfilAttribute($value)
    {
        if (is_resource($value)) {
            @rewind($value);
            $value = stream_get_contents($value);
        }

        if ($value === null || $value === false) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
