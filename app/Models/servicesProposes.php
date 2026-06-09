<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class servicesProposes extends Model
{
    //
    protected $table = 'services_proposes';

    protected $fillable = [
        'nom_service', 'description','lien', 'image', 'admin_id'
    ];

    public function admin() {
        return $this->belongsTo(Admins::class, 'admin_id');
    }
}
