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
        Schema::create('actualites', function (Blueprint $table) {
        $table->id();
        $table->string('auteur_nom');
        $table->text('contenu');
        $table->date('date_publication');
        $table->string('source')->nullable();
        $table->string('type_media')->nullable();
        $table->string('url_media')->nullable();
        $table->binary('image')->nullable();
        $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
        $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actualites');
    }
};
