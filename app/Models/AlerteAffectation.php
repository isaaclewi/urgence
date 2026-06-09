<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AlerteAffectation extends Model
{


protected $table = 'alerte_affectations';



protected $fillable = [

'alerte_id',

'service_source_id',

'service_destination_id',

'affecte_par',

'statut',

'commentaire',

'date_affectation'

];



protected $casts = [

'date_affectation'=>'datetime'

];



public function alerte()
{
    return $this->belongsTo(
        Alertes::class,
        'alerte_id'
    );
}



public function serviceSource()
{
    return $this->belongsTo(
        Services::class,
        'service_source_id'
    );
}



public function serviceDestination()
{
    return $this->belongsTo(
        Services::class,
        'service_destination_id'
    );
}



public function agent()
{
    return $this->belongsTo(
        Admins::class,
        'affecte_par'
    );
}


}
