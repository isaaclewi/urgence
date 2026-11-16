<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agent extends Model
{
    protected $table = 'agents';

    protected $fillable = [
        'service_id', 'equipe_id', 'matricule', 'nom', 'prenom', 'fonction',
        'grade', 'telephone', 'email', 'statut', 'competences', 'date_embauche',
        'password', 'compte_actif', 'doit_changer_mdp', 'derniere_connexion',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'date_embauche' => 'date',
        'compte_actif' => 'boolean',
        'doit_changer_mdp' => 'boolean',
        'derniere_connexion' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function equipe()
    {
        return $this->belongsTo(Services::class, 'equipe_id');
    }

    public function gardes()
    {
        return $this->hasMany(Garde::class, 'agent_id');
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    /**
     * Matricule automatique et unique sur toute la plateforme : TYPE + n° du service, année, numéro d'ordre.
     * Ex. POL3-26-0001 (le n° du service évite qu'un même matricule existe dans deux services,
     * ce qui permet la connexion par matricule).
     */
    public static function genererMatricule(Services $service): string
    {
        $base = Str::of((string) ($service->role ?: $service->nom))->ascii()->upper()
            ->replaceMatches('/[^A-Z]/', '')->substr(0, 3)->toString() ?: 'AGT';

        $motif = $base . $service->id . '-' . now()->format('y') . '-';

        $dernier = static::where('service_id', $service->id)
            ->where('matricule', 'like', $motif . '%')
            ->pluck('matricule')
            ->map(fn ($m) => (int) substr($m, strlen($motif)))
            ->max() ?? 0;

        return $motif . str_pad((string) ($dernier + 1), 4, '0', STR_PAD_LEFT);
    }

    public function peutSeConnecter(): bool
    {
        return $this->compte_actif && $this->password && $this->statut !== 'inactif';
    }
}
