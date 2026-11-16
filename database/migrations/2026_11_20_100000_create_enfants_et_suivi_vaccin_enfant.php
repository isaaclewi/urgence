<?php

use App\Support\SuiviSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SuiviSchema::creer();   // ne crée que les tables qui manquent
    }

    public function down(): void
    {
        Schema::dropIfExists('suivi_vaccin_enfant');
        Schema::dropIfExists('enfants');
    }
};
