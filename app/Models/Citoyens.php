<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Citoyens extends Model
{
    use HasFactory;
    use \App\Models\Concerns\NormalisePhotoProfil;

    protected $table = 'citoyens';

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'sexe',
        'adresse',
        'email',
        'telephone',
        'pseudo',
        'etat_compte',
        'piece_identite',
        'photo_profil',
        'password',
        // vérification d'identité
        'date_naissance',
        'type_piece',
        'numero_piece',
        'numero_piece_hash',
        'date_expiration_piece',
        'selfie_identite',
        'statut_verification',
        'motif_rejet',
        'verifie_par',
        'verifie_le',
        'email_verifie_le',
    ];

    protected $hidden = [
        'password',
        'numero_piece',
        'numero_piece_hash',
    ];

    protected $casts = [
        'date_naissance'        => 'date',
        'date_expiration_piece' => 'date',
        'verifie_le'            => 'datetime',
        'email_verifie_le'      => 'datetime',
    ];

    public function alertes()
    {
        return $this->hasMany(
            alertes::class,
            'citoyen_id'
        );
    }

    /** Âge calculé : toujours juste, contrairement à un âge saisi une fois. */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }

    /** Numéro de pièce masqué (seuls les 4 derniers caractères sont visibles). */
    public function numeroPieceMasque(): ?string
    {
        if (! $this->numero_piece) {
            return null;
        }

        try {
            $clair = Crypt::decryptString($this->numero_piece);
        } catch (\Throwable $e) {
            return null;
        }

        return str_repeat('•', max(0, strlen($clair) - 4)) . substr($clair, -4);
    }

    /** Niveau de confiance affiché : non_verifie | email | identite */
    public function niveauConfiance(): string
    {
        if ($this->statut_verification === 'verifie') {
            return 'identite';
        }

        return $this->email_verifie_le ? 'email' : 'non_verifie';
    }
}
