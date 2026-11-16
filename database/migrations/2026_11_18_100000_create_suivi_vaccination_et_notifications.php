<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Inscriptions aux programmes de vaccination (citoyens ET personnel) ──
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

        // ── Notifications internes (citoyen, agent, service/équipe) ──
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

    public function down(): void
    {
        Schema::dropIfExists('notifications_app');
        Schema::dropIfExists('inscriptions_vaccin');
    }
};
