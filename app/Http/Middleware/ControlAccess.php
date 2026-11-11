<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Appliqué au groupe « web » : vérifie la permission des routes listées dans
 * config/permissions.php. Si personne n'est connecté sur le portail, on laisse
 * passer : c'est le middleware auth.* de la route qui redirige vers la connexion.
 */
class ControlAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $nom    = $request->route()?->getName();
        $portal = Access::portalForRoute($nom);

        if ($portal && Access::isLoggedIn($portal)) {
            $permission = Access::permissionForRoute($nom, $request->method());

            if ($permission && ! Access::allows($permission, $portal)) {
                abort(403, "Votre type de compte n'a pas accès à cette section.");
            }
        }

        return $next($request);
    }
}
