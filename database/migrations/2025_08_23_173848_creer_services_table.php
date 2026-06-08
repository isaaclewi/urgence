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
        Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // Nom du service
        $table->string('adresse')->nullable();
        $table->string('role'); // hôpital, police, pompier, mairie
        $table->boolean('disponible')->default(true);
        $table->string('email')->nullable();
        $table->string('etat_compte')->default('actif');
        $table->string('telephone')->nullable();
        $table->string('password');
        $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->binary('photo_profil')->nullable()->after('password'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('photo_profil');
        });
    }
};
