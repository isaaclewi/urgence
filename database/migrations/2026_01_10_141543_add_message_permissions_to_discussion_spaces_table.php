<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('discussion_spaces', function (Blueprint $table) {

            // Autorisation d’écrire par rôle
            $table->boolean('citizens_can_post')->default(true);
            $table->boolean('moderators_can_post')->default(true);
            $table->boolean('agents_can_post')->default(true);

        });
    }

    public function down(): void
    {
        Schema::table('discussion_spaces', function (Blueprint $table) {
            $table->dropColumn([
                'citizens_can_post',
                'moderators_can_post',
                'agents_can_post',
            ]);
        });
    }
};

