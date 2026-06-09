<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Alertes extends Model
{

    protected $table = "alertes";


    protected $fillable = [

        'titre',
        'description',
        'date_heure',
        'localisation',

        'latitude',
        'longitude',

        'media_photo',
        'media_vocal',

        'statut',
        'statut_intervention',

        'type_alerte',

        'citoyen_id',

        'services_id',

        'equipe_id'
    ];



    protected $casts = [

        'date_heure'=>'datetime',

        'latitude'=>'decimal:8',

        'longitude'=>'decimal:8'

    ];



    public function citoyen()
    {
        return $this->belongsTo(
            Citoyens::class,
            'citoyen_id'
        );
    }



    public function service()
    {
        return $this->belongsTo(
            Services::class,
            'services_id'
        );
    }



    public function equipe()
    {
        return $this->belongsTo(
            Services::class,
            'equipe_id'
        );
    }



    public function affectations()
    {
        return $this->hasMany(
            AlerteAffectation::class,
            'alerte_id'
        );
    }



    public function interventions()
    {
        return $this->hasMany(
            AlerteIntervention::class,
            'alerte_id'
        );
    }

}
