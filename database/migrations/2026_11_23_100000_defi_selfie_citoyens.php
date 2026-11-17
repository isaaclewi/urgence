<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citoyens', function (Blueprint $table) {
            $table->text('defi_selfie')->nullable();            // consigne demandée (code + geste)
            $table->timestamp('selfie_pris_le')->nullable();    // moment où le selfie a été envoyé
            $table->boolean('selfie_direct')->default(false);   // déclaré pris avec la caméra (et non importé)
        });
    }

    public function down(): void
    {
        Schema::table('citoyens', function (Blueprint $table) {
            $table->dropColumn(['defi_selfie', 'selfie_pris_le', 'selfie_direct']);
        });
    }
};
