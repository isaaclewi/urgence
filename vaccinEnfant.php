<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinEnfant extends Model
{
    use HasFactory;

    // Nom de la table dans la base de données
    protected $table = 'vaccin_enfants';

    // Champs remplissables
    protected $fillable = [
        'nom_vaccin',
        'fabricant',
        'nombre_doses',
        'voie_administration',
        'date_fabrication',
        'date_expiration',
        'age_cible_min',
        'age_cible_max',
        'vaccination_obligatoire',
        'programme_id',
    ];

    // Champs de type date (pour conversion automatique en objets Carbon)
    protected $dates = [
        'date_fabrication',
        'date_expiration',
        'created_at',
        'updated_at',
    ];

}
