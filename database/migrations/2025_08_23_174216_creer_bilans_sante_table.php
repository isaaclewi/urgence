<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bilans_sante', function (Blueprint $table) {
        $table->id();
        $table->foreignId('citoyen_id')->constrained('citoyens')->cascadeOnDelete();


        $table->text('allergies')->nullable();
        $table->text('groupe_sanguin')->nullable();
        $table->text('taille')->nullable();
        $table->text('poids')->nullable();
        $table->text('antecedents_familiaux')->nullable();
        $table->text('mode_de_vie')->nullable();
        $table->text('maladies_chroniques')->nullable();
        $table->text('maladies_passees_importantes')->nullable();
        $table->text('interventions_chirurgicales')->nullable();
        $table->text('antecedents_hospitalisation')->nullable();
        $table->text('medicaments_pris_actuellement')->nullable();
        $table->text('listez_vaccins_reçus')->nullable();


        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilans_sante');
    }
};
