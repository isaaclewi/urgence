<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->string('telephone_contact', 30)->nullable();   // numéro pour rappeler la personne
            $table->boolean('critique')->default(false)->index();  // « danger immédiat » coché par la personne
        });
    }

    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropColumn(['telephone_contact', 'critique']);
        });
    }
};
