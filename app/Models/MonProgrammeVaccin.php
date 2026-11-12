<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonProgrammeVaccin extends Model
{
    use HasFactory;

    protected $table = 'mon_programmes_vaccin';

    protected $fillable = [
        'nom_programme', 'age_cible', 'date_debut', 'date_fin',
        'nb_vaccins', 'organisme_resp', 'categorie', 'service_id',
    ];

    // $dates n'existe plus dans Laravel 12 : on utilise les casts
    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'nb_vaccins' => 'integer',
        'service_id' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function vaccins()
    {
        return $this->hasMany(vaccinEnfant::class, 'programme_id');
    }

    /** Programme créé par ce service (sinon : donnée nationale, en lecture seule). */
    public function appartientA(int $serviceId): bool
    {
        return $this->service_id !== null && (int) $this->service_id === $serviceId;
    }

    public function scopeAVenir(Builder $q): Builder
    {
        return $q->whereDate('date_debut', '>', today());
    }

    public function scopeTermines(Builder $q): Builder
    {
        return $q->whereDate('date_fin', '<', today());
    }

    public function scopeEnCours(Builder $q): Builder
    {
        return $q
            ->where(fn ($w) => $w->whereNull('date_debut')->orWhereDate('date_debut', '<=', today()))
            ->where(fn ($w) => $w->whereNull('date_fin')->orWhereDate('date_fin', '>=', today()))
            ->where(fn ($w) => $w->whereNotNull('date_debut')->orWhereNotNull('date_fin'));
    }

    /** À venir | En cours | Terminé | Sans dates */
    public function statut(): string
    {
        if (! $this->date_debut && ! $this->date_fin) {
            return 'Sans dates';
        }
        if ($this->date_debut && $this->date_debut->gt(today())) {
            return 'À venir';
        }
        if ($this->date_fin && $this->date_fin->lt(today())) {
            return 'Terminé';
        }

        return 'En cours';
    }

    public function statutType(): string
    {
        return match ($this->statut()) {
            'En cours' => 'success',
            'À venir'  => 'info',
            default    => 'neutral',
        };
    }
}
