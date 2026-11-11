<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'agents';

    protected $fillable = [
        'service_id', 'equipe_id', 'matricule', 'nom', 'prenom', 'fonction',
        'grade', 'telephone', 'email', 'statut', 'competences', 'date_embauche',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function equipe()
    {
        return $this->belongsTo(Services::class, 'equipe_id');
    }

    public function gardes()
    {
        return $this->hasMany(Garde::class, 'agent_id');
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
