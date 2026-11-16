<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviVaccinEnfant extends Model
{
    protected $table = 'suivi_vaccin_enfant';

    protected $fillable = ['enfant_id', 'vaccin_id', 'doses_recues', 'derniere_dose', 'prochaine_dose'];

    protected $casts = [
        'doses_recues'   => 'integer',
        'derniere_dose'  => 'date',
        'prochaine_dose' => 'date',
    ];

    public function enfant()
    {
        return $this->belongsTo(Enfant::class, 'enfant_id');
    }

    public function vaccin()
    {
        return $this->belongsTo(vaccinEnfant::class, 'vaccin_id');
    }
}
