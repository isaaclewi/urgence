<?php

namespace App\Console\Commands;

use App\Models\Admins;
use App\Models\Citoyens;
use App\Models\Services;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

/**
 * php artisan connexion:diagnostic
 * php artisan connexion:diagnostic --portail=admin --email=moi@mail.com --password=secret
 *
 * Vérifie tout ce qui peut empêcher une connexion : configuration de session et de cookie,
 * base de données, cache, tables des comptes, et (optionnel) un identifiant précis.
 */
class DiagnosticConnexion extends Command
{
    protected $signature = 'connexion:diagnostic
                            {--portail= : citoyen, admin, service ou equipe}
                            {--email= : email du compte à tester}
                            {--password= : mot de passe à tester}';

    protected $description = 'Diagnostique les problèmes de connexion (session, cookie, base, cache, comptes)';

    private int $problemes = 0;

    public function handle(): int
    {
        $this->info('=== Diagnostic de connexion ===');

        $this->verifierConfiguration();
        $this->verifierBase();
        $this->verifierSession();
        $this->verifierCache();
        $this->verifierComptes();

        if ($this->option('email')) {
            $this->verifierIdentifiants();
        }

        $this->newLine();
        $this->problemes === 0
            ? $this->info('Aucun problème détecté côté serveur. Si la connexion reste bloquée, vide le cache du navigateur (F12 > Application > Clear site data) et regarde F12 > Réseau.')
            : $this->warn("{$this->problemes} problème(s) détecté(s) : corrige les lignes KO ci-dessus.");

        return $this->problemes === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function ok(string $texte): void
    {
        $this->line("  <info>OK </info> {$texte}");
    }

    private function ko(string $texte): void
    {
        $this->problemes++;
        $this->line("  <error> KO </error> {$texte}");
    }

    private function note(string $texte): void
    {
        $this->line("  <comment>... </comment>{$texte}");
    }

    private function verifierConfiguration(): void
    {
        $this->newLine();
        $this->info('1. Configuration');

        config('app.key') ? $this->ok('APP_KEY définie') : $this->ko("APP_KEY vide : lance php artisan key:generate");

        $url    = (string) config('app.url');
        $schema = parse_url($url, PHP_URL_SCHEME);
        $this->note("APP_ENV=" . config('app.env') . " | APP_URL={$url}");

        $driver = (string) config('session.driver');
        $valides = ['file', 'cookie', 'database', 'apc', 'memcached', 'redis', 'dynamodb', 'array'];
        in_array($driver, $valides, true)
            ? $this->ok("SESSION_DRIVER={$driver}")
            : $this->ko("SESSION_DRIVER={$driver} n'existe pas : mets SESSION_DRIVER=database (ou file) dans .env");

        if (config('session.secure') === true && $schema === 'http') {
            $this->ko("SESSION_SECURE_COOKIE=true alors que l'application tourne en http : le navigateur refuse le cookie, donc la connexion « ne se fait pas ». Mets SESSION_SECURE_COOKIE=false en local.");
        } else {
            $this->ok('Cookie de session compatible avec le schéma de APP_URL');
        }

        $domaine = config('session.domain');
        if ($domaine && ! in_array($domaine, ['null', ''], true)) {
            $this->note("SESSION_DOMAIN={$domaine} : doit correspondre à l'adresse utilisée dans le navigateur (en local, laisse-le vide).");
        }
    }

    private function verifierBase(): void
    {
        $this->newLine();
        $this->info('2. Base de données');

        try {
            $debut = microtime(true);
            DB::select('select 1');
            $ms = round((microtime(true) - $debut) * 1000);
            $this->ok("Connexion '" . config('database.default') . "' établie en {$ms} ms");

            if ($ms > 500) {
                $this->note("Réponse lente ({$ms} ms) : avec des sessions en base, chaque page fait plusieurs requêtes. Sur une base distante, utilise SESSION_DRIVER=file en local.");
            }
        } catch (\Throwable $e) {
            $this->ko('Base inaccessible : ' . $e->getMessage());
        }
    }

    private function verifierSession(): void
    {
        $this->newLine();
        $this->info('3. Stockage des sessions');

        $driver = (string) config('session.driver');

        try {
            if ($driver === 'database') {
                $table = config('session.table', 'sessions');
                Schema::hasTable($table)
                    ? $this->ok("Table '{$table}' présente (" . DB::table($table)->count() . ' sessions)')
                    : $this->ko("Table '{$table}' absente : lance php artisan migrate");
            } elseif ($driver === 'file') {
                $dossier = config('session.files');
                is_dir($dossier) && is_writable($dossier)
                    ? $this->ok("Dossier {$dossier} accessible en écriture")
                    : $this->ko("Dossier {$dossier} introuvable ou non inscriptible");
            } else {
                $this->note("Pas de vérification spécifique pour le driver {$driver}");
            }
        } catch (\Throwable $e) {
            $this->ko('Sessions : ' . $e->getMessage());
        }
    }

    private function verifierCache(): void
    {
        $this->newLine();
        $this->info('4. Cache et limitation des tentatives (ProtectLogins)');

        try {
            $debut = microtime(true);
            Cache::put('diag-connexion', 'ok', 10);
            $lu = Cache::get('diag-connexion');
            Cache::forget('diag-connexion');
            RateLimiter::hit('diag-connexion-limiteur', 5);
            RateLimiter::clear('diag-connexion-limiteur');
            $ms = round((microtime(true) - $debut) * 1000);

            $lu === 'ok'
                ? $this->ok("Cache '" . config('cache.default') . "' lisible/écrivable ({$ms} ms)")
                : $this->ko("Le cache '" . config('cache.default') . "' n'a pas relu la valeur écrite");
        } catch (\Throwable $e) {
            $this->ko("Cache '" . config('cache.default') . "' en erreur : " . $e->getMessage()
                . " (la connexion fonctionne quand même grâce à la tolérance de ProtectLogins, mais corrige CACHE_STORE ou lance php artisan migrate)");
        }
    }

    private function verifierComptes(): void
    {
        $this->newLine();
        $this->info('5. Tables des comptes');

        try {
            $this->ok('Citoyens : ' . Citoyens::count() . ' compte(s)');
        } catch (\Throwable $e) {
            $this->ko('Citoyens : ' . $e->getMessage());
        }

        try {
            $this->ok('Services : ' . Services::whereNull('parent_service_id')->count() . ' service(s), '
                . Services::whereNotNull('parent_service_id')->count() . ' équipe(s)');
        } catch (\Throwable $e) {
            $this->ko('Services : ' . $e->getMessage());
        }

        try {
            $table = (new Admins())->getTable();
            $this->ok("Admins : table '{$table}', " . Admins::count() . ' compte(s)');
        } catch (\Throwable $e) {
            $this->ko('Admins : ' . $e->getMessage());
        }
    }

    private function verifierIdentifiants(): void
    {
        $this->newLine();
        $this->info('6. Test des identifiants');

        $portail  = (string) $this->option('portail');
        $email    = (string) $this->option('email');
        $password = (string) $this->option('password');

        if (! in_array($portail, ['citoyen', 'admin', 'service', 'equipe'], true)) {
            $this->ko("Précise --portail=citoyen|admin|service|equipe");
            return;
        }

        try {
            $compte = match ($portail) {
                'citoyen' => Citoyens::where('email', $email)->first(),
                'admin'   => Admins::where('email', $email)->first(),
                'service' => Services::where('email', $email)->whereNull('parent_service_id')->first(),
                'equipe'  => Services::where('email', $email)->where('type_compte', 'equipe')->first(),
            };

            if (! $compte) {
                $this->ko("Aucun compte « {$portail} » avec l'email {$email} (vérifie le portail choisi et l'orthographe).");
                return;
            }

            $this->ok("Compte trouvé (id {$compte->id})");

            $hash = $portail === 'admin' ? $compte->mot_passe : $compte->password;

            if (! $hash) {
                $this->ko('Ce compte n\'a pas de mot de passe enregistré.');
            } elseif ($password === '') {
                $this->note('Ajoute --password=... pour tester le mot de passe.');
            } elseif (Hash::check($password, $hash)) {
                $this->ok('Mot de passe correct');
            } else {
                $this->ko('Mot de passe incorrect pour ce compte.');
            }

            $etat = $compte->etat_compte ?? null;
            if ($etat !== null && $etat !== 'actif') {
                $this->ko("Compte non actif (etat_compte = {$etat}) : la connexion sera refusée.");
            }
        } catch (\Throwable $e) {
            $this->ko('Test impossible : ' . $e->getMessage());
        }
    }
}
