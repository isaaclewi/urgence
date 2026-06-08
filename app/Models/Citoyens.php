<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citoyens extends Model
{
    use HasFactory;

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
    ];

    protected $hidden = [
        'password',
    ];
}
