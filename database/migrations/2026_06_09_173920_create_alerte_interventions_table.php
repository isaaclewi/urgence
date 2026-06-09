<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


public function up(): void
{


Schema::create('alerte_interventions', function(Blueprint $table){


$table->id();



$table->foreignId('alerte_id')
    ->constrained('alertes')
    ->cascadeOnDelete();



$table->foreignId('equipe_id')
    ->constrained('services')
    ->cascadeOnDelete();



$table->text('commentaire')
    ->nullable();



$table->decimal('latitude',10,8)
    ->nullable();



$table->decimal('longitude',11,8)
    ->nullable();



$table->string('statut')
    ->nullable();



$table->timestamps();



});


}



public function down(): void
{

Schema::dropIfExists('alerte_interventions');

}


};
