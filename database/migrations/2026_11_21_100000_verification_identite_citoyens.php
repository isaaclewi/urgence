<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citoyens', function (Blueprint $table) {
            $table->date('date_naissance')->nullable();
            $table->string('type_piece', 20)->nullable();              // cni | passeport | permis
            $table->text('numero_piece')->nullable();                  // chiffré
            $table->string('numero_piece_hash', 64)->nullable()->unique(); // empreinte : un même numéro = un seul compte
            $table->date('date_expiration_piece')->nullable();
            $table->string('selfie_identite')->nullable();             // chemin du selfie chiffré
            $table->string('statut_verification', 20)->default('en_attente')->index(); // en_attente | verifie | rejete
            $table->text('motif_rejet')->nullable();
            $table->unsignedBigInteger('verifie_par')->nullable();     // id de l'admin
            $table->timestamp('verifie_le')->nullable();
            $table->timestamp('email_verifie_le')->nullable();
        });

        // Les comptes déjà actifs ont été contrôlés à la main : on ne les bloque pas.
        DB::table('citoyens')
            ->where('etat_compte', 'actif')
            ->update(['statut_verification' => 'verifie', 'verifie_le' => now()]);

        Schema::create('verification_journal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('citoyen_id')->index();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('action', 40);
            $table->text('detail')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_journal');

        Schema::table('citoyens', function (Blueprint $table) {
            $table->dropUnique(['numero_piece_hash']);
            $table->dropColumn([
                'date_naissance', 'type_piece', 'numero_piece', 'numero_piece_hash',
                'date_expiration_piece', 'selfie_identite', 'statut_verification',
                'motif_rejet', 'verifie_par', 'verifie_le', 'email_verifie_le',
            ]);
        });
    }
};
