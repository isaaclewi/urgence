<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bilanSante extends Model
{
    use HasFactory;

    protected $table = 'bilans_sante';

    /**
     * Champs du formulaire => colonne en base. Tous sont chiffrés (encrypt()/decrypt(), comme
     * avant : les bilans déjà enregistrés restent lisibles). La colonne des vaccins porte un
     * caractère accentué ; le formulaire utilise « vaccins_recus ».
     */
    public const COLONNES = [
        'groupe_sanguin'                => 'groupe_sanguin',
        'taille'                        => 'taille',
        'poids'                         => 'poids',
        'allergies'                     => 'allergies',
        'maladies_chroniques'           => 'maladies_chroniques',
        'maladies_passees_importantes'  => 'maladies_passees_importantes',
        'interventions_chirurgicales'   => 'interventions_chirurgicales',
        'antecedents_hospitalisation'   => 'antecedents_hospitalisation',
        'antecedents_familiaux'         => 'antecedents_familiaux',
        'medicaments_pris_actuellement' => 'medicaments_pris_actuellement',
        'vaccins_recus'                 => 'listez_vaccins_reçus',
        'mode_de_vie'                   => 'mode_de_vie',
    ];

    protected $fillable = [
        'citoyen_id',
        'allergies', 'groupe_sanguin', 'taille', 'poids',
        'antecedents_familiaux', 'mode_de_vie', 'maladies_chroniques',
        'maladies_passees_importantes', 'interventions_chirurgicales',
        'antecedents_hospitalisation', 'medicaments_pris_actuellement',
        'listez_vaccins_reçus',
        'service_id', 'modifie_par_service_id',
    ];

    public function citoyen()
    {
        return $this->belongsTo(Citoyens::class, 'citoyen_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function modifiePar()
    {
        return $this->belongsTo(Services::class, 'modifie_par_service_id');
    }

    public function creePar(int $serviceId): bool
    {
        return $this->service_id !== null && (int) $this->service_id === $serviceId;
    }

    /** Valeurs déchiffrées, indexées par nom de champ du formulaire. */
    public function valeurs(): array
    {
        $out = [];

        foreach (self::COLONNES as $cle => $colonne) {
            $brut = $this->getAttribute($colonne);

            if ($brut === null || $brut === '') {
                $out[$cle] = null;
                continue;
            }

            try {
                $out[$cle] = (string) decrypt($brut);
            } catch (DecryptException) {
                $out[$cle] = '⚠ Illisible (donnée non chiffrée ou clé différente)';
            }
        }

        return $out;
    }

    /** Un seul champ déchiffré (pour la liste, sans tout déchiffrer). */
    public function valeur(string $cle): ?string
    {
        return $this->valeurSimple($cle);
    }

    private function valeurSimple(string $cle): ?string
    {
        $brut = $this->getAttribute(self::COLONNES[$cle] ?? $cle);

        if ($brut === null || $brut === '') {
            return null;
        }

        try {
            return (string) decrypt($brut);
        } catch (DecryptException) {
            return null;
        }
    }

    /** Données du formulaire => colonnes chiffrées, prêtes à enregistrer. */
    public static function versColonnes(array $donnees): array
    {
        $out = [];

        foreach (self::COLONNES as $cle => $colonne) {
            $v = $donnees[$cle] ?? null;
            $out[$colonne] = ($v === null || trim((string) $v) === '') ? null : encrypt(trim((string) $v));
        }

        return $out;
    }
}
