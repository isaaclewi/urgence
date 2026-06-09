<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

public function up(): void
{


Schema::create('alerte_affectations', function(Blueprint $table){


    $table->id();


    $table->foreignId('alerte_id')
        ->constrained('alertes')
        ->cascadeOnDelete();



    $table->foreignId('service_source_id')
        ->nullable()
        ->constrained('services')
        ->nullOnDelete();



    $table->foreignId('service_destination_id')
        ->constrained('services')
        ->cascadeOnDelete();



    $table->foreignId('affecte_par')
        ->nullable();



    $table->string('statut')
        ->default('transmise');


    $table->text('commentaire')
        ->nullable();



    $table->timestamp('date_affectation')
        ->useCurrent();



    $table->timestamps();


});


}



public function down(): void
{

Schema::dropIfExists('alerte_affectations');

}


};
