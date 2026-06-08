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
        Schema::create('alertes', function (Blueprint $table) {
        $table->id();
        $table->string('titre');
        $table->text('description')->nullable();
        $table->dateTime('date_heure');
        $table->string('localisation')->nullable();
        $table->string('media_photo')->nullable();
        $table->string('statut')->default('ouvert');
        $table->string('type_alerte')->nullable();


        $table->foreignId('citoyen_id')->nullable()->constrained('citoyens')->nullOnDelete();
        $table->foreignId('services_id')->nullable()->constrained('hopitaux')->nullOnDelete();


        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
