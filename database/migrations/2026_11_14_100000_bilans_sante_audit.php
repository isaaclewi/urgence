<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Traçabilité des bilans de santé : quel service a créé / modifié un bilan,
     * et journal de chaque consultation (donnée de santé = on sait qui a regardé quoi).
     */
    public function up(): void
    {
        Schema::table('bilans_sante', function (Blueprint $table) {
            if (! Schema::hasColumn('bilans_sante', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->foreignId('modifie_par_service_id')->nullable()->constrained('services')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('bilan_journal')) {
            Schema::create('bilan_journal', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bilan_id')->nullable();   // pas de clé étrangère : on garde la trace après suppression
                $table->unsignedBigInteger('citoyen_id');
                $table->unsignedBigInteger('service_id')->nullable();
                $table->string('action', 20);                         // consultation | creation | modification | suppression
                $table->timestamp('created_at')->useCurrent();

                $table->index(['bilan_id', 'created_at']);
                $table->index('citoyen_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bilan_journal');

        Schema::table('bilans_sante', function (Blueprint $table) {
            $table->dropConstrainedForeignId('modifie_par_service_id');
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
