<?php

/*
|--------------------------------------------------------------------------
| Rôles et permissions
|--------------------------------------------------------------------------
| Les comptes restent ceux de l'application (session admin / service / équipe).
| Ce fichier décide ce que chaque type de compte peut faire. Pour changer un
| droit, modifie simplement ce fichier (puis php artisan config:clear).
|
|  - admin   : tout.
|  - equipe  : uniquement le terrain (déjà isolé par le middleware auth.equipe).
|  - service : liste ci-dessous, moins les droits retirés selon le type de
|              service (colonne services.role : hôpital, police, pompier…).
*/

return [

    // Permissions accordées à tout compte « service »
    'service' => [
        'urgences.view', 'urgences.manage',
        'interventions.view',
        'equipes.manage',
        'personnel.view', 'personnel.manage',
        'ressources.view', 'ressources.manage',
        'kpi.view',
        'exports.view',
        'citoyens.view',
        'bilans.view', 'bilans.manage',
        'vaccination.view', 'vaccination.manage',
        'actualites.manage',
        'forum.access',
    ],

    // Droits retirés selon le type de service (valeur de services.role, sans accents, en minuscules).
    // Par défaut : la police et les pompiers n'accèdent pas aux bilans de santé ni à la vaccination.
    'service_deny' => [
        'police'  => ['bilans.view', 'bilans.manage', 'vaccination.view', 'vaccination.manage'],
        'pompier' => ['bilans.view', 'bilans.manage', 'vaccination.view', 'vaccination.manage'],
    ],

    // Permissions de l'équipe terrain (informatif : ses routes sont déjà isolées)
    'equipe' => ['*'],

    // Correspondance nom de route => [permission en lecture (GET), permission en écriture (POST/PUT/DELETE)]
    // Le premier motif qui correspond l'emporte. Une route absente de cette liste n'est pas contrôlée.
    'routes' => [
        'services.urgence*'            => ['urgences.view',      'urgences.manage'],
        'services.interventions.*'     => ['interventions.view', 'interventions.view'],
        'services.equipes.*'           => ['equipes.manage',     'equipes.manage'],
        'services.personnel.*'         => ['personnel.view',     'personnel.manage'],
        'services.ressources.*'        => ['ressources.view',    'ressources.manage'],
        'services.kpi*'                => ['kpi.view',           'kpi.view'],
        'services.exports.*'           => ['exports.view',       'exports.view'],
        'services.citoyens'            => ['citoyens.view',      'citoyens.view'],
        'services.citoyensBilan*'      => ['bilans.view',        'bilans.manage'],
        'services.vaccination*'        => ['vaccination.view',   'vaccination.manage'],
        'services.actualite*'          => ['actualites.manage',  'actualites.manage'],
        'services.forum.*'             => ['forum.access',       'forum.access'],
    ],
];
