<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (! Schema::hasColumn('agents', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (! Schema::hasColumn('agents', 'compte_actif')) {
                $table->boolean('compte_actif')->default(true)->after('password');
            }
            if (! Schema::hasColumn('agents', 'doit_changer_mdp')) {
                $table->boolean('doit_changer_mdp')->default(true)->after('compte_actif');
            }
            if (! Schema::hasColumn('agents', 'derniere_connexion')) {
                $table->timestamp('derniere_connexion')->nullable()->after('doit_changer_mdp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['password', 'compte_actif', 'doit_changer_mdp', 'derniere_connexion']);
        });
    }
};
