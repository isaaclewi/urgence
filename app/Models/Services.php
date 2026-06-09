<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $table = 'services';


    protected $fillable = [
        'nom',
        'adresse',
        'role',
        'disponible',
        'email',
        'etat_compte',
        'telephone',
        'password',
        'photo_profil',
        'parent_service_id',
        'type_compte'
    ];


    /**
     * Service parent
     * Exemple : Brigade appartient à Police
     */
    public function parent()
    {
        return $this->belongsTo(
            Services::class,
            'parent_service_id'
        );
    }



    /**
     * Sous-services / équipes
     */
    public function equipes()
    {
        return $this->hasMany(
            Services::class,
            'parent_service_id'
        );
    }



    public function alertes()
    {
        return $this->hasMany(
            Alertes::class,
            'services_id'
        );
    }



    /**
     * Alertes affectées directement à cette équipe
     */
    public function alertesEquipe()
    {
        return $this->hasMany(
            Alertes::class,
            'equipe_id'
        );
    }



    public function affectationsEnvoyees()
    {
        return $this->hasMany(
            AlerteAffectation::class,
            'service_source_id'
        );
    }



    public function affectationsRecues()
    {
        return $this->hasMany(
            AlerteAffectation::class,
            'service_destination_id'
        );
    }



    public function interventions()
    {
        return $this->hasMany(
            AlerteIntervention::class,
            'equipe_id'
        );
    }



    public function discussionSpaces()
    {
        return $this->hasMany(
            DiscussionSpace::class
        );
    }

}
