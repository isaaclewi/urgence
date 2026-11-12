<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chaque programme et chaque vaccin appartient désormais à un service.
     * Les lignes existantes gardent service_id = NULL : ce sont des données « nationales »
     * visibles par tous les services, mais modifiables par personne depuis le portail service.
     */
    public function up(): void
    {
        foreach (['mon_programmes_vaccin', 'vaccin_enfants'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (! Schema::hasColumn($table, 'service_id')) {
                    $t->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['mon_programmes_vaccin', 'vaccin_enfants'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropConstrainedForeignId('service_id');
            });
        }
    }
};
