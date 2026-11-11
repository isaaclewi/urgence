<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('gardes');
        Schema::dropIfExists('agents');
    }
};
