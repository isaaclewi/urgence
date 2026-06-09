<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AlerteIntervention extends Model
{


protected $table = 'alerte_interventions';



protected $fillable = [

'alerte_id',

'equipe_id',

'commentaire',

'latitude',

'longitude',

'statut'

];



protected $casts = [

'latitude'=>'decimal:8',

'longitude'=>'decimal:8'

];



public function alerte()
{
    return $this->belongsTo(
        alertes::class,
        'alerte_id'
    );
}



public function equipe()
{
    return $this->belongsTo(
        Services::class,
        'equipe_id'
    );
}


}
