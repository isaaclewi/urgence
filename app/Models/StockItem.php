<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'service_id', 'categorie', 'designation', 'lot', 'quantite',
        'unite', 'seuil_alerte', 'date_expiration',
    ];

    protected $casts = [
        'date_expiration' => 'date',
    ];

    public function estSousSeuil(): bool
    {
        return $this->seuil_alerte > 0 && $this->quantite <= $this->seuil_alerte;
    }

    public function estExpire(): bool
    {
        return $this->date_expiration && $this->date_expiration->lt(now()->startOfDay());
    }

    public function expireBientot(): bool
    {
        return $this->date_expiration
            && ! $this->estExpire()
            && $this->date_expiration->lte(now()->addDays(30));
    }
}
