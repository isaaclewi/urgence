<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BilanJournal extends Model
{
    protected $table = 'bilan_journal';

    public $timestamps = false;

    protected $fillable = ['bilan_id', 'citoyen_id', 'service_id', 'action'];

    protected $casts = ['created_at' => 'datetime'];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public static function noter(int $citoyenId, ?int $bilanId, ?int $serviceId, string $action): void
    {
        static::create([
            'bilan_id'   => $bilanId,
            'citoyen_id' => $citoyenId,
            'service_id' => $serviceId,
            'action'     => $action,
        ]);
    }
}
