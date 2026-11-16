<?php

namespace App\Support;

use App\Models\Enfant;
use App\Models\vaccinEnfant;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calendrier vaccinal d'un enfant : pour chaque vaccin, où en est-il ?
 * États : en_retard | a_faire | a_venir | complet
 */
class CalendrierEnfant
{
    public const LIBELLES = [
        'en_retard' => 'En retard',
        'a_faire'   => 'À faire maintenant',
        'a_venir'   => 'À venir',
        'complet'   => 'Terminé',
    ];

    public const PILLS = [
        'en_retard' => 'pill-red',
        'a_faire'   => 'pill-yellow',
        'a_venir'   => 'pill-blue',
        'complet'   => 'pill-green',
    ];

    private const ORDRE = ['en_retard' => 0, 'a_faire' => 1, 'a_venir' => 2, 'complet' => 3];

    /** @return Collection<int, array> */
    public static function pour(Enfant $enfant): Collection
    {
        $suivis = $enfant->suivis()->get()->keyBy('vaccin_id');
        $age    = $enfant->ageEnMois();

        return vaccinEnfant::orderByDesc('vaccination_obligatoire')->orderBy('age_cible_min')->get()
            ->map(function ($v) use ($enfant, $suivis, $age) {
                $suivi  = $suivis->get($v->id);
                $total  = max(1, (int) ($v->nombre_doses ?: 1));
                $recues = (int) ($suivi->doses_recues ?? 0);
                $min    = $v->age_cible_min;
                $max    = $v->age_cible_max;

                $echeance = null;   // date à laquelle la prochaine dose est attendue

                if ($recues >= $total) {
                    $etat = 'complet';
                } elseif ($recues > 0 && $suivi?->prochaine_dose) {
                    $echeance = $suivi->prochaine_dose;
                    $etat = $echeance->lt(today()) ? 'en_retard' : ($echeance->lte(today()->addDays(7)) ? 'a_faire' : 'a_venir');
                } elseif ($min !== null && $age < $min) {
                    $etat     = 'a_venir';
                    $echeance = Carbon::parse($enfant->date_naissance)->addMonths((int) $min);
                } elseif ($max !== null && $age > $max) {
                    $etat = 'en_retard';
                } else {
                    $etat = 'a_faire';
                }

                return [
                    'vaccin'   => $v,
                    'suivi'    => $suivi,
                    'total'    => $total,
                    'recues'   => $recues,
                    'etat'     => $etat,
                    'libelle'  => self::LIBELLES[$etat],
                    'pill'     => self::PILLS[$etat],
                    'echeance' => $echeance,
                ];
            })
            ->sortBy(fn ($l) => self::ORDRE[$l['etat']])
            ->values();
    }

    /**
     * Prévient le parent d'un vaccin à faire ou en retard, une seule fois par situation
     * (pas de cron : le contrôle se fait à l'ouverture des pages « enfants »).
     */
    public static function rappeler(Enfant $enfant, Collection $lignes): void
    {
        foreach ($lignes->whereIn('etat', ['a_faire', 'en_retard'])->take(5) as $l) {
            $retard  = $l['etat'] === 'en_retard';
            $message = $enfant->prenom . ' : ' . $l['vaccin']->nom_vaccin
                . ($retard ? ' est en retard.' : ' est à faire maintenant.')
                . ' (dose ' . ($l['recues'] + 1) . '/' . $l['total'] . ')';

            $deja = \App\Models\NotificationApp::where('destinataire_type', 'citoyen')
                ->where('destinataire_id', $enfant->citoyen_id)
                ->where('categorie', 'vaccin')
                ->where('message', $message)
                ->exists();

            if (! $deja) {
                Notifier::citoyen(
                    $enfant->citoyen_id,
                    $retard ? 'Vaccin en retard' : 'Vaccin à faire',
                    $message,
                    route('enfants.show', $enfant->id, false),
                    $retard ? 'important' : 'info',
                    'vaccin'
                );
            }
        }
    }
}
