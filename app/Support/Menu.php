<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Source unique des menus des trois portails (layout portal ET anciens masters).
 * Un lien n'apparaît que si sa route existe ET que le compte connecté y a droit
 * (voir config/permissions.php).
 *
 * Format d'un item : [route, libellé, icône Feather, motif(s) d'activation]
 */
class Menu
{
    public static function definition(): array
    {
        return [
        'service' => [
            'Pilotage' => [
                ['services.compte',                 'Tableau de bord', 'home',     'services.compte'],
                ['services.urgenceSignalee',        'Urgences',        'alert-triangle', 'services.urgenceSignalee*'],
                ['services.interventions.journalieres', 'Interventions', 'activity', 'services.interventions.*'],
            ],
            'Organisation' => [
                ['services.equipes.index',          'Équipes',         'users',    'services.equipes.*'],
                ['services.personnel.index',        'Personnel',       'user-check', ['services.personnel.index','services.personnel.create','services.personnel.edit']],
                ['services.personnel.gardes',       'Planning des gardes', 'calendar', 'services.personnel.gardes*'],
                ['services.ressources.index',       'Ressources',      'truck',    'services.ressources.*'],
                ['services.kpi',                    'Indicateurs',     'bar-chart-2', 'services.kpi*'],
                ['services.exports.index',          'Rapports & exports', 'download', 'services.exports.*'],
            ],
            'Santé & citoyens' => [
                ['services.citoyens',               'Citoyens',        'user',     'services.citoyens'],
                ['services.citoyensBilan',          'Bilans de santé', 'heart',    'services.citoyensBilan*'],
                ['services.vaccinationIndex',       'Vaccination',     'shield',   'services.vaccination*'],
            ],
            'Mon compte' => [
                ['services.profil',                 'Gestion interne', 'settings', 'services.profil*'],
            ],
            'Communication' => [
                ['services.actualite',              'Actualités',      'file-text','services.actualite*'],
                ['services.messagerie.index',       'Messagerie',      'message-circle', 'services.messagerie.*'],
            ],
        ],
        'admin' => [
            'Administration' => [
                ['admin.compte',                    'Tableau de bord', 'home',     'admin.compte'],
                ['admin.users',                     'Citoyens',        'users',    'admin.users*'],
                ['admin.valider',                   'Vérifications',   'check-circle', 'admin.valider'],
                ['admin.services',                  'Services',        'briefcase','admin.service*'],
                ['admin.alertes',                   'Urgences',        'bell',     'admin.alertes'],
                ['admin.bilanSante',                'Bilans de santé', 'heart',    'admin.bilanSante'],
            ],
            'Contenu' => [
                ['admin.actualite',                 'Actualités',      'file-text','admin.actualite*'],
                ['admin.messagerie.index',          'Messagerie',      'message-circle', ['admin.messagerie.index','admin.messagerie.show']],
                ['admin.messagerie.gestion',        'Gérer les groupes','message-square', ['admin.messagerie.gestion*','admin.messagerie.membres*']],
            ],
        ],
        'equipe' => [
            'Terrain' => [
                ['equipe.dashboard',                'Tableau de bord', 'home',     'equipe.dashboard'],
                ['equipe.alertes',                  'Mes alertes',     'alert-triangle', 'equipe.alerte*'],
                ['equipe.profil',                   'Profil',          'user',     'equipe.profil'],
            ],
        ],
    ];
    }

    /** Sections visibles pour un portail : [section => [[route, label, icon, active], …]] */
    public static function sections(string $portal): array
    {
        $resultat = [];

        foreach ((self::definition()[$portal] ?? []) as $section => $items) {
            $visibles = [];

            foreach ($items as [$route, $label, $icon, $motif]) {
                if (! Route::has($route) || ! Access::canRoute($route)) {
                    continue;
                }

                $visibles[] = [
                    'route'  => $route,
                    'label'  => $label,
                    'icon'   => $icon,
                    'active' => request()->routeIs(...(array) $motif),
                ];
            }

            if ($visibles) {
                $resultat[$section] = $visibles;
            }
        }

        return $resultat;
    }
}
