<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvisUser extends Model
{
    use \App\Models\Concerns\NormalisePhotoProfil;

    protected $table = 'avis_users';
    protected $fillable = ['citoyen_id', 'nom', 'message', 'photo_profil'];

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class);
    }
}
