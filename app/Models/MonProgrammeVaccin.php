<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonProgrammeVaccin extends Model
{
    use HasFactory;

    // Nom de la table si différent de la convention (ici 'mon_programmes_vaccin')
    protected $table = 'mon_programmes_vaccin';

    // Champs assignables en masse
    protected $fillable = [
        'nom_programme',
        'age_cible',
        'date_debut',
        'date_fin',
        'nb_vaccins',
        'organisme_resp',
        'categorie',
    ];

    // Pour caster automatiquement les dates
    protected $dates = [
        'date_debut',
        'date_fin',
        'created_at',
        'updated_at',
    ];
}
