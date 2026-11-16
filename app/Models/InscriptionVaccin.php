<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscriptionVaccin extends Model
{
    public const STATUTS = ['inscrit', 'en_cours', 'termine', 'abandonne'];

    public const LIBELLES = [
        'inscrit'   => 'Inscrit',
        'en_cours'  => 'En cours',
        'termine'   => 'Terminé',
        'abandonne' => 'Abandonné',
    ];

    protected $table = 'inscriptions_vaccin';

    protected $fillable = [
        'programme_id', 'citoyen_id', 'agent_id', 'statut',
        'doses_recues', 'derniere_dose', 'prochaine_dose', 'note',
    ];

    protected $casts = [
        'doses_recues'   => 'integer',
        'derniere_dose'  => 'date',
        'prochaine_dose' => 'date',
    ];

    public function programme()
    {
        return $this->belongsTo(MonProgrammeVaccin::class, 'programme_id');
    }

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class, 'citoyen_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function libelleStatut(): string
    {
        return self::LIBELLES[$this->statut] ?? ucfirst((string) $this->statut);
    }

    /** Type de badge du design system. */
    public function badgeType(): string
    {
        return match ($this->statut) {
            'termine'   => 'success',
            'en_cours'  => 'info',
            'abandonne' => 'neutral',
            default     => 'warning',
        };
    }

    /** Avancement en % (0 si le programme n'indique pas de nombre de doses). */
    public function progression(): int
    {
        $total = (int) ($this->programme?->nb_vaccins ?? 0);

        if ($this->statut === 'termine') {
            return 100;
        }

        return $total > 0 ? (int) min(100, round($this->doses_recues * 100 / $total)) : 0;
    }

    public function libelleDoses(): string
    {
        $total = $this->programme?->nb_vaccins;

        return $total ? "{$this->doses_recues} / {$total} dose(s)" : "{$this->doses_recues} dose(s)";
    }

    public function sujetNom(): string
    {
        if ($this->agent) {
            return $this->agent->nom_complet . ' (' . $this->agent->matricule . ')';
        }

        return $this->citoyen
            ? trim(($this->citoyen->prenom ?? '') . ' ' . ($this->citoyen->nom ?? ''))
            : '—';
    }

    public function sujetType(): string
    {
        return $this->agent_id ? 'Personnel' : 'Citoyen';
    }
}
