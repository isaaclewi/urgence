<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Les colonnes photo_profil avaient été créées en BINAIRE (bytea sous PostgreSQL/Supabase)
 * alors que le code y enregistre une URL (texte).
 * Conséquence : à la lecture, PDO renvoie une « ressource » et non du texte, donc
 * « Resource id #12 » s'affichait à la place de la photo et les profils ne se mettaient pas à jour.
 * Cette migration convertit ces colonnes en TEXT en conservant les URL déjà enregistrées.
 */
return new class extends Migration
{
    private array $cibles = ['citoyens', 'admins', 'agents', 'services', 'avis_users'];

    public function up(): void
    {
        $driver = DB::getDriverName();

        foreach ($this->cibles as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'photo_profil')) {
                continue;
            }

            if ($driver === 'pgsql') {
                $type = DB::selectOne(
                    'select data_type from information_schema.columns
                     where table_schema = current_schema() and table_name = ? and column_name = ?',
                    [$table, 'photo_profil']
                )->data_type ?? null;

                if ($type !== 'bytea') {
                    continue;
                }

                try {
                    DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"photo_profil\" TYPE text USING convert_from(\"photo_profil\", 'UTF8')");
                } catch (\Throwable $e) {
                    Log::warning("photo_profil de {$table} : conversion directe impossible, colonne remise à NULL. " . $e->getMessage());
                    DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"photo_profil\" TYPE text USING NULL");
                }
            } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `photo_profil` TEXT NULL");
            }
            // SQLite : pas de typage strict, rien à faire.
        }
    }

    public function down(): void
    {
        // Volontairement vide : revenir au binaire réintroduirait le bug.
    }
};
