<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discussion_spaces', function (Blueprint $table) {
            $table->id();

            // Infos de base
            $table->string('title');
            $table->text('description')->nullable();

            // Type et statut
            $table->enum('type', ['public', 'prive'])->default('public');
            $table->boolean('is_active')->default(true);

            // Créateur (agent admin)
            $table->foreignId('created_by')
                ->constrained('agents')
                ->onDelete('cascade');

            // Service responsable / modérateur
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_spaces');
    }
};
