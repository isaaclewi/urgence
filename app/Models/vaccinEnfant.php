<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinEnfant extends Model
{
    use HasFactory;

    /** Un lot est signalé « à surveiller » quand il expire dans moins de N jours. */
    public const ALERTE_JOURS = 60;

    protected $table = 'vaccin_enfants';

    protected $fillable = [
        'nom_vaccin', 'fabricant', 'nombre_doses', 'voie_administration',
        'date_fabrication', 'date_expiration', 'age_cible_min', 'age_cible_max',
        'vaccination_obligatoire', 'programme_id', 'service_id',
    ];

    protected $casts = [
        'date_fabrication'        => 'date',
        'date_expiration'         => 'date',
        'nombre_doses'            => 'integer',
        'age_cible_min'           => 'integer',
        'age_cible_max'           => 'integer',
        'vaccination_obligatoire' => 'boolean',
        'service_id'              => 'integer',
    ];

    public function programme()
    {
        return $this->belongsTo(MonProgrammeVaccin::class, 'programme_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function appartientA(int $serviceId): bool
    {
        return $this->service_id !== null && (int) $this->service_id === $serviceId;
    }

    public function scopePerimes(Builder $q): Builder
    {
        return $q->whereDate('date_expiration', '<', today());
    }

    public function scopeBientotPerimes(Builder $q): Builder
    {
        return $q->whereDate('date_expiration', '>=', today())
                 ->whereDate('date_expiration', '<=', today()->addDays(self::ALERTE_JOURS));
    }

    public function scopeValides(Builder $q): Builder
    {
        return $q->where(fn ($w) => $w->whereNull('date_expiration')
            ->orWhereDate('date_expiration', '>', today()->addDays(self::ALERTE_JOURS)));
    }

    /** Périmé | Expire bientôt | Valide | Non renseigné */
    public function peremption(): string
    {
        if (! $this->date_expiration) {
            return 'Non renseigné';
        }
        if ($this->date_expiration->lt(today())) {
            return 'Périmé';
        }
        if ($this->date_expiration->lte(today()->addDays(self::ALERTE_JOURS))) {
            return 'Expire bientôt';
        }

        return 'Valide';
    }

    public function peremptionType(): string
    {
        return match ($this->peremption()) {
            'Périmé'         => 'danger',
            'Expire bientôt' => 'warning',
            'Valide'         => 'success',
            default          => 'neutral',
        };
    }
}
