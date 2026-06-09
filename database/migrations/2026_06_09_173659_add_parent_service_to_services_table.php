<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->foreignId('parent_service_id')
                ->nullable()
                ->after('id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->string('type_compte')
                ->default('service')
                ->after('role');

        });

    }


    public function down(): void
    {

        Schema::table('services', function (Blueprint $table) {

            $table->dropForeign([
                'parent_service_id'
            ]);

            $table->dropColumn([
                'parent_service_id',
                'type_compte'
            ]);

        });

    }

};
