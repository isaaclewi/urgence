<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Admins extends Model
{
   use HasFactory;
    use \App\Models\Concerns\NormalisePhotoProfil;

   /**
    * Table des comptes administrateur.
    * Le dépôt a connu deux noms : « admins » (migration creer_admin_table) et « agents »
    * (ancien nom utilisé par ce modèle). Or « agents » sert maintenant aussi au module Personnel,
    * qui n'a ni mot de passe ni compte de connexion. On choisit donc la table qui possède
    * la colonne mot_passe, en préférant celle qui contient déjà des comptes.
    */
   public function getTable()
   {
      static $table = null;

      if ($table !== null) {
         return $table;
      }

      $candidates = [];
      foreach (['admins', 'agents'] as $nom) {
         if (Schema::hasTable($nom) && Schema::hasColumn($nom, 'mot_passe')) {
            $candidates[] = $nom;
         }
      }

      foreach ($candidates as $nom) {
         if (\Illuminate\Support\Facades\DB::table($nom)->exists()) {
            return $table = $nom;
         }
      }

      return $table = ($candidates[0] ?? 'admins');
   }

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

   public function affectations()
{
    return $this->hasMany(
        AlerteAffectation::class,
        'affecte_par'
    );
}
}
