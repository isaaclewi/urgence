<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Alertes extends Model
{

    protected $table = "alertes";


    /** Une nouvelle urgence prévient immédiatement le service concerné (notification + sonnerie). */
    protected static function booted(): void
    {
        static::created(function ($alerte) {
            \App\Support\Notifier::service(
                $alerte->services_id ? (int) $alerte->services_id : null,
                'Nouvelle urgence',
                trim(($alerte->titre ?: ($alerte->type_alerte ?: 'Alerte')) . ($alerte->localisation ? ' — ' . $alerte->localisation : '')),
                route('services.urgenceSignalee', [], false),
                'urgent',
                'alerte'
            );
        });
    }

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

        'equipe_id',

        'telephone_contact',
        'critique',
    ];



    protected $casts = [

        'date_heure'=>'datetime',

        'critique'=>'boolean',

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
