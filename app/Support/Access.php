<?php

namespace App\Support;

use App\Models\Services;
use Illuminate\Support\Str;

/**
 * Contrôle d'accès léger basé sur les sessions existantes (admin / service / équipe)
 * et sur config/permissions.php.
 */
class Access
{
    private const SESSION_KEYS = [
        'admin'   => 'admin_id',
        'service' => 'service_id',
        'equipe'  => 'equipe_id',
    ];

    /** Portail concerné par une route, d'après son nom. */
    public static function portalForRoute(?string $name): ?string
    {
        if (! $name) {
            return null;
        }

        return match (true) {
            str_starts_with($name, 'admin.')    => 'admin',
            str_starts_with($name, 'equipe.')   => 'equipe',
            str_starts_with($name, 'services.') => 'service',
            default                             => null,
        };
    }

    public static function isLoggedIn(string $portal): bool
    {
        $key = self::SESSION_KEYS[$portal] ?? null;

        return $key !== null && session()->has($key);
    }

    /** Type du service connecté (hopital, police, pompier…), sans accents, en minuscules. */
    public static function serviceType(): string
    {
        $role = session('service_role');

        if ($role === null && session('service_id')) {
            $role = (string) Services::whereKey(session('service_id'))->value('role');
            session(['service_role' => $role]);
        }

        return Str::of((string) $role)->ascii()->lower()->toString();
    }

    /** Permission requise pour une route et une méthode HTTP, ou null si la route n'est pas contrôlée. */
    public static function permissionForRoute(string $name, string $method = 'GET'): ?string
    {
        $lecture = in_array(strtoupper($method), ['GET', 'HEAD'], true);

        foreach ((array) config('permissions.routes', []) as $motif => [$read, $write]) {
            if (Str::is($motif, $name)) {
                return $lecture ? $read : $write;
            }
        }

        return null;
    }

    /** Le compte connecté sur ce portail possède-t-il cette permission ? */
    public static function allows(string $permission, string $portal): bool
    {
        if (! self::isLoggedIn($portal)) {
            return false;
        }

        if ($portal === 'admin') {
            return true;
        }

        if ($portal === 'equipe') {
            $liste = (array) config('permissions.equipe', []);

            return in_array('*', $liste, true) || in_array($permission, $liste, true);
        }

        $accordees = (array) config('permissions.service', []);
        $retirees  = (array) (config('permissions.service_deny', [])[self::serviceType()] ?? []);

        return in_array($permission, $accordees, true) && ! in_array($permission, $retirees, true);
    }

    /** Usage dans les vues : @access('personnel.manage') … @endaccess */
    public static function can(string $permission): bool
    {
        foreach (array_keys(self::SESSION_KEYS) as $portal) {
            if (self::allows($permission, $portal)) {
                return true;
            }
        }

        return false;
    }

    /** Le lien vers cette route doit-il apparaître dans le menu ? */
    public static function canRoute(string $name): bool
    {
        $permission = self::permissionForRoute($name, 'GET');
        $portal     = self::portalForRoute($name);

        if ($permission === null || $portal === null) {
            return true;
        }

        return self::allows($permission, $portal);
    }
}
