<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    protected $table = 'vehicules';

    protected $fillable = [
        'service_id', 'equipe_id', 'type', 'immatriculation', 'marque_modele',
        'statut', 'kilometrage', 'prochaine_maintenance',
    ];

    protected $casts = [
        'prochaine_maintenance' => 'date',
    ];

    public function equipe()
    {
        return $this->belongsTo(Services::class, 'equipe_id');
    }

    /** Maintenance dépassée ou prévue dans les 15 jours. */
    public function maintenanceProche(): bool
    {
        return $this->prochaine_maintenance && $this->prochaine_maintenance->lte(now()->addDays(15));
    }
}
