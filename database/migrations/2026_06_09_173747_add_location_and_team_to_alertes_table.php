<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('alertes', function (Blueprint $table) {


            $table->decimal('latitude',10,8)
                ->nullable()
                ->after('localisation');


            $table->decimal('longitude',11,8)
                ->nullable()
                ->after('latitude');


            $table->string('statut_intervention')
                ->default('nouvelle')
                ->after('statut');


            $table->foreignId('equipe_id')
                ->nullable()
                ->after('services_id')
                ->constrained('services')
                ->nullOnDelete();


        });

    }



    public function down(): void
    {

        Schema::table('alertes', function (Blueprint $table) {


            $table->dropForeign([
                'equipe_id'
            ]);


            $table->dropColumn([
                'latitude',
                'longitude',
                'statut_intervention',
                'equipe_id'
            ]);


        });

    }

};
