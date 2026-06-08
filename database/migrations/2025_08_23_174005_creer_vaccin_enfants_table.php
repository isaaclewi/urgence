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
        Schema::create('vaccin_enfants', function (Blueprint $table) {
        $table->id();
        // Attributs vaccin (abstrait)
        $table->string('nom_vaccin');
        $table->string('fabricant')->nullable();
        $table->integer('nombre_doses')->nullable();
        $table->string('voie_administration')->nullable();
        $table->date('date_fabrication')->nullable();
        $table->date('date_expiration')->nullable();


        // Spécifiques
        $table->integer('age_cible_min')->nullable();
        $table->integer('age_cible_max')->nullable();
        $table->boolean('vaccination_obligatoire')->default(false);


        // Programme
        $table->foreignId('programme_id')->nullable()->constrained('mon_programmes_vaccin')->nullOnDelete();


        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccin_enfants');
    }
};
