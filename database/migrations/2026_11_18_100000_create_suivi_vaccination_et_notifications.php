<?php

use App\Support\SuiviSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SuiviSchema::creer();
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_app');
        Schema::dropIfExists('inscriptions_vaccin');
    }
};
