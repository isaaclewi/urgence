<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Pages de l'espace citoyen proposables comme « service ».
 * L'administrateur choisit une page dans une liste ; le lien est généré automatiquement
 * à partir du nom de la route (donc toujours correct, en local comme en ligne).
 */
class LiensServices
{
    /** clé => [libellé, nom de route, description proposée] */
    public const PAGES = [
        'police'      => ['Alerte Police',            'alertePolice.index',            'Signaler une urgence à la police.'],
        'pompier'     => ['Alerte Pompiers',          'alertePompier.index',           'Signaler un incendie ou un accident aux sapeurs-pompiers.'],
        'hopital'     => ['Alerte Hôpital / Ambulance','alerteHopital.index',          'Demander une assistance médicale ou une ambulance.'],
        'suivi'       => ['Suivi de mes alertes',     'SuiviAlertesController',        'Suivre l\'état de vos alertes en cours.'],
        'vaccination' => ['Vaccination',              'vaccinationMenuController',     'Consulter les programmes et le suivi des vaccinations.'],
        'bilan'       => ['Mon bilan de santé',       'bilanController',               'Consulter votre dossier médical personnel.'],
        'actualites'  => ['Actualités',               'actualitesController',          'Lire les informations publiées par les services.'],
        'avis'        => ['Avis des utilisateurs',    'avisUsers.index',               'Donner votre avis sur la plateforme.'],
        'qr'          => ['Code QR santé',            'codeQR',                        'Afficher votre code QR d\'identification.'],
        'profil'      => ['Mon profil',               'profilController',              'Modifier vos informations personnelles.'],
    ];

    /** Liste pour le <select> (seules les routes qui existent réellement). */
    public static function options(): array
    {
        $liste = [];
        foreach (self::PAGES as $cle => [$libelle, $route, $description]) {
            if (Route::has($route)) {
                $liste[$cle] = ['libelle' => $libelle, 'description' => $description];
            }
        }

        return $liste;
    }

    /**
     * Construit le lien. Sans « site » : lien relatif (/alertePolice) qui marche partout.
     * Avec « site » (ex. http://127.0.0.1:8000/) : lien complet.
     */
    public static function lien(string $cle, ?string $site = null): string
    {
        [, $route] = self::PAGES[$cle];
        $chemin = route($route, [], false);

        $site = trim((string) $site);

        return $site === '' ? $chemin : rtrim($site, '/') . $chemin;
    }
}
