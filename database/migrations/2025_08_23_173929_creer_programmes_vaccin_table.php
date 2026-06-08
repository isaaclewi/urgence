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
        Schema::create('mon_programmes_vaccin', function (Blueprint $table) {
        $table->id();
        $table->string('nom_programme');
        $table->string('age_cible')->nullable(); // ex: "0-5 ans"
        $table->date('date_debut')->nullable();
        $table->date('date_fin')->nullable();
        $table->integer('nb_vaccins')->nullable();
        $table->string('organisme_resp')->nullable();
        $table->timestamps();
        });

        Schema::table('mon_programmes_vaccin', function (Blueprint $table) {
            $table->string('categorie')->nullable()->after('organisme_resp'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_programmes_vaccin');

        Schema::table('mon_programmes_vaccin', function (Blueprint $table) {
            $table->dropColumn('categorie');
        });
    }
};
