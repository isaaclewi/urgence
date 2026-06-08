<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class bilanSante extends Model
{
    use HasFactory;

    protected $table = 'bilans_sante';

    protected $fillable = [
        'citoyen_id',
        'allergies',
        'groupe_sanguin',
        'taille',
        'poids',
        'antecedents_familiaux',
        'mode_de_vie',
        'maladies_chroniques',
        'maladies_passees_importantes',
        'interventions_chirurgicales',
        'antecedents_hospitalisation',
        'medicaments_pris_actuellement',
        'listez_vaccins_reçus',
    ];

    // dans App\Models\bilanSante.php
    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class);
    }
}
