<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enfant extends Model
{
    protected $table = 'enfants';

    protected $fillable = ['citoyen_id', 'prenom', 'nom', 'sexe', 'date_naissance'];

    protected $casts = ['date_naissance' => 'date'];

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class, 'citoyen_id');
    }

    public function suivis()
    {
        return $this->hasMany(SuiviVaccinEnfant::class, 'enfant_id');
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . ($this->nom ?? ''));
    }

    /** Âge en mois révolus. */
    public function ageEnMois(): int
    {
        $n = $this->date_naissance;
        $t = today();
        $mois = ($t->year - $n->year) * 12 + ($t->month - $n->month) - ($t->day < $n->day ? 1 : 0);

        return max(0, $mois);
    }

    public function libelleAge(): string
    {
        $m = $this->ageEnMois();

        if ($m < 24) {
            return $m . ' mois';
        }

        $ans = intdiv($m, 12);
        $reste = $m % 12;

        return $ans . ' an' . ($ans > 1 ? 's' : '') . ($reste ? " et {$reste} mois" : '');
    }
}
