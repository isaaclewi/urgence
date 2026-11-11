<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── agents ────────────────────────────────────────────
        if (Schema::hasTable('agents')) {
            // Table déjà créée (ex. tentative précédente) : on la garde si elle a nos colonnes,
            // sinon on s'arrête pour ne rien écraser par erreur.
            if (! Schema::hasColumns('agents', ['service_id', 'matricule', 'nom', 'prenom', 'fonction', 'statut'])) {
                throw new RuntimeException(
                    "Une table « agents » différente existe déjà (colonnes : "
                    . implode(', ', Schema::getColumnListing('agents'))
                    . "). Renomme-la ou supprime-la avant de relancer la migration."
                );
            }
        } else {
            Schema::create('agents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->foreignId('equipe_id')->nullable()->constrained('services')->nullOnDelete();
                $table->string('matricule', 50);
                $table->string('nom', 100);
                $table->string('prenom', 100);
                $table->string('fonction', 100);
                $table->string('grade', 100)->nullable();
                $table->string('telephone', 30)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('statut', 20)->default('actif');
                $table->text('competences')->nullable();
                $table->date('date_embauche')->nullable();
                $table->timestamps();

                $table->unique(['service_id', 'matricule']);
                $table->index(['service_id', 'statut']);
            });
        }

        // ── gardes ────────────────────────────────────────────
        if (! Schema::hasTable('gardes')) {
            Schema::create('gardes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
                $table->string('type', 20)->default('jour');
                $table->dateTime('date_debut');
                $table->dateTime('date_fin');
                $table->string('note', 255)->nullable();
                $table->timestamps();

                $table->index(['service_id', 'date_debut', 'date_fin']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gardes');
        Schema::dropIfExists('agents');
    }
};
