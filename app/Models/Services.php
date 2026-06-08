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
        'photo_profil'
    ];

    public function discussionSpaces()
    {
        return $this->hasMany(DiscussionSpace::class);
    }
}
