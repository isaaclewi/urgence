<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services_proposes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_service');
            $table->text('description')->nullable();
            $table->text('lien')->nullable();
            $table->timestamps();
        });

      Schema::table('services_proposes', function (Blueprint $table) {
    $table->longText('image')->nullable();
});



         Schema::table('services_proposes', function (Blueprint $table) {
        $table->unsignedBigInteger('admin_id')->nullable()->after('image');

        // Si tu veux ajouter la relation de clé étrangère
        $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('services_proposes');

         Schema::table('services_proposes', function (Blueprint $table) {
            $table->dropColumn('image');
        });

         Schema::table('services_proposes', function (Blueprint $table) {
        $table->dropForeign(['admin_id']);
        $table->dropColumn('admin_id');
    });
    }
};