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
        Schema::create('citoyens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe', 10); 
            $table->string('adresse');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('pseudo')->nullable();
            $table->string('etat_compte')->default('desactif');

            //Changement de binary en "string" afin de gérer le chiffrement et le stockage des images en base64
            $table->string('piece_identite')->nullable();
            $table->string('password');
            $table->timestamps();
        });

        Schema::table('citoyens', function (Blueprint $table) {
            $table->binary('photo_profil')->nullable()->after('piece_identite'); 

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citoyens');
        
        Schema::table('citoyens', function (Blueprint $table) {
            $table->dropColumn('photo_profil');
        });
    }
};
