<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discussion_messages', function (Blueprint $table) {
            $table->id();

            /**
             * Lien avec l’espace de discussion
             */
            $table->foreignId('discussion_space_id')
                ->constrained('discussion_spaces')
                ->onDelete('cascade');

            /**
             * Auteur du message
             * (citoyen, agent ou service)
             */
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['citoyen', 'agent', 'service']);

            /**
             * Type de message
             */
            $table->enum('message_type', [
                'texte',
                'image',
                'video',
                'fichier'
            ])->default('texte');

            /**
             * Contenu texte
             */
            $table->text('message')->nullable();

            /**
             * Pièce jointe (image, vidéo, fichier)
             */
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime')->nullable();
            $table->integer('file_size')->nullable();

            /**
             * Statut du message
             */
            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_messages');
    }
};
