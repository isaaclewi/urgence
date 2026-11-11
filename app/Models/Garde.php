<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garde extends Model
{
    protected $table = 'gardes';

    protected $fillable = ['service_id', 'agent_id', 'type', 'date_debut', 'date_fin', 'note'];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function estEnCours(): bool
    {
        return $this->date_debut->lte(now()) && $this->date_fin->gte(now());
    }
}
