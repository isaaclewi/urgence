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
        'media_photo',
        'media_vocal', // ✅ nouveau champ
        'statut',
        'type_alerte',
        'citoyen_id',
        'services_id',
    ];

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class);
    }

    public function services()
    {
        return $this->belongsTo(Services::class);
    }
}
