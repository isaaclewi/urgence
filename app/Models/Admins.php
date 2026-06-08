<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admins extends Model
{
   use HasFactory;

   protected $table = "agents";

   protected $fillable = [
      'matricule',
      'nom',
      'prenom',
      'sexe',
      'adresse',
      'email',
      'telephone',
      'etat_compte',
      'photo_profil',
      'role',
      'mot_passe',
   ];

   public function discussionSpaces()
   {
      return $this->hasMany(DiscussionSpace::class, 'created_by');
   }
}
