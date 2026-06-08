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
        Schema::create('agents', function (Blueprint $table) {
        $table->id();
        $table->string('matricule')->unique();
        $table->string('nom');
        $table->string('prenom');
        $table->string('sexe', 10);
        $table->string('adresse')->nullable();
        $table->string('email')->unique();
        $table->string('telephone')->nullable();
        $table->string('etat_compte')->default('actif');
        $table->string('mot_passe'); // hashé si tu l’utilises directement ici
        $table->string('role')->default('admin');
        $table->timestamps();
});
        Schema::table('agents', function (Blueprint $table) {
            $table->binary('photo_profil')->nullable()->after('role'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('photo_profil');
        });
    }
};
