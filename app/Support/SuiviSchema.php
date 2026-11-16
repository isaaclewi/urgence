<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables du suivi de vaccination et des notifications.
 * Utilisé par la migration ET en secours à l'ouverture des pages : si la migration n'a pas été
 * exécutée (ou a échoué sans bruit au démarrage), les tables manquantes sont créées à ce moment.
 */
class SuiviSchema
{
    private static bool $verifie = false;

    public static function assurer(): void
    {
        if (self::$verifie) {
            return;
        }

        self::creer();
        self::$verifie = true;
    }

    public static function creer(): void
    {
        if (! Schema::hasTable('inscriptions_vaccin')) {
            Schema::create('inscriptions_vaccin', function (Blueprint $table) {
                $table->id();
                $table->foreignId('programme_id')->constrained('mon_programmes_vaccin')->cascadeOnDelete();
                $table->foreignId('citoyen_id')->nullable()->constrained('citoyens')->cascadeOnDelete();
                $table->foreignId('agent_id')->nullable()->constrained('agents')->cascadeOnDelete();
                $table->string('statut', 20)->default('inscrit');   // inscrit | en_cours | termine | abandonne
                $table->unsignedInteger('doses_recues')->default(0);
                $table->date('derniere_dose')->nullable();
                $table->date('prochaine_dose')->nullable();
                $table->string('note', 255)->nullable();
                $table->timestamps();

                $table->unique(['programme_id', 'citoyen_id']);
                $table->unique(['programme_id', 'agent_id']);
                $table->index('statut');
            });
        }

        if (! Schema::hasTable('enfants')) {
            Schema::create('enfants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('citoyen_id')->constrained('citoyens')->cascadeOnDelete();
                $table->string('prenom', 80);
                $table->string('nom', 80)->nullable();
                $table->string('sexe', 1)->nullable();              // M | F
                $table->date('date_naissance');
                $table->timestamps();

                $table->index('citoyen_id');
            });
        }

        if (! Schema::hasTable('suivi_vaccin_enfant')) {
            Schema::create('suivi_vaccin_enfant', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
                $table->foreignId('vaccin_id')->constrained('vaccin_enfants')->cascadeOnDelete();
                $table->unsignedInteger('doses_recues')->default(0);
                $table->date('derniere_dose')->nullable();
                $table->date('prochaine_dose')->nullable();
                $table->timestamps();

                $table->unique(['enfant_id', 'vaccin_id']);
            });
        }

        if (! Schema::hasTable('notifications_app')) {
            Schema::create('notifications_app', function (Blueprint $table) {
                $table->id();
                $table->string('destinataire_type', 20);            // citoyen | agent | service (un service ou une équipe)
                $table->unsignedBigInteger('destinataire_id');
                $table->string('categorie', 30)->default('systeme'); // alerte | mission | vaccin | bilan | garde | systeme
                $table->string('niveau', 12)->default('info');       // info | important | urgent
                $table->string('titre', 150);
                $table->text('message');
                $table->string('lien', 255)->nullable();
                $table->timestamp('lu_at')->nullable();
                $table->timestamps();

                $table->index(['destinataire_type', 'destinataire_id', 'lu_at'], 'notif_destinataire_lu');
            });
        }
    }
}
