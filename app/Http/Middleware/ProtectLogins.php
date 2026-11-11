<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protège les 3 formulaires de connexion (citoyen, admin, service/équipe) :
 *  - limite à 5 échecs par minute pour un même email + même adresse IP ;
 *  - régénère l'identifiant de session après une connexion réussie
 *    (protection contre la fixation de session).
 * Appliqué au groupe « web » : aucune route à modifier.
 */
class ProtectLogins
{
    private const ROUTES = ['processLogin', 'admin.login.process', 'services.login.process'];
    private const MAX_ECHECS = 5;
    private const DUREE = 60; // secondes

    public function handle(Request $request, Closure $next): Response
    {
        $nom = $request->route()?->getName();

        if (! $request->isMethod('POST') || ! in_array($nom, self::ROUTES, true)) {
            return $next($request);
        }

        $cle = 'login|' . $nom . '|' . sha1(strtolower((string) $request->input('email'))) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($cle, self::MAX_ECHECS)) {
            $secondes = RateLimiter::availableIn($cle);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Trop de tentatives de connexion. Réessayez dans {$secondes} secondes."]);
        }

        $reponse = $next($request);

        // Échec = le contrôleur vient de flasher des erreurs pour la requête suivante.
        $echec = in_array('errors', (array) $request->session()->get('_flash.new', []), true);

        if ($echec) {
            RateLimiter::hit($cle, self::DUREE);
        } else {
            RateLimiter::clear($cle);
            $request->session()->regenerate();
        }

        return $reponse;
    }
}
