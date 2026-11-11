<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('equipe_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('type', 30);
            $table->string('immatriculation', 30)->unique();
            $table->string('marque_modele', 120)->nullable();
            $table->string('statut', 20)->default('disponible');
            $table->unsignedInteger('kilometrage')->nullable();
            $table->date('prochaine_maintenance')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'statut']);
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('categorie', 30);
            $table->string('designation', 150);
            $table->string('lot', 60)->nullable();
            $table->unsignedInteger('quantite')->default(0);
            $table->string('unite', 20)->nullable();
            $table->unsignedInteger('seuil_alerte')->default(0);
            $table->date('date_expiration')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'categorie']);
            $table->index('date_expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('vehicules');
    }
};
