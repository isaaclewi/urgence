<?php

namespace App\Support;

use App\Models\NotificationApp;
use Illuminate\Support\Facades\Log;

/**
 * Envoi et lecture des notifications internes.
 * Un envoi n'interrompt jamais l'action principale : en cas d'erreur, on journalise seulement.
 */
class Notifier
{
    public static function envoyer(
        string $type,
        ?int $id,
        string $titre,
        string $message,
        ?string $lien = null,
        string $niveau = 'info',
        string $categorie = 'systeme'
    ): void {
        if (! $id) {
            return;
        }

        try {
            NotificationApp::create([
                'destinataire_type' => $type,
                'destinataire_id'   => $id,
                'categorie'         => $categorie,
                'niveau'            => in_array($niveau, ['info', 'important', 'urgent'], true) ? $niveau : 'info',
                'titre'             => mb_substr($titre, 0, 150),
                'message'           => $message,
                'lien'              => $lien,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Notification non enregistrée', ['erreur' => $e->getMessage(), 'titre' => $titre]);
        }
    }

    public static function citoyen(?int $id, string $titre, string $message, ?string $lien = null, string $niveau = 'info', string $categorie = 'systeme'): void
    {
        self::envoyer('citoyen', $id, $titre, $message, $lien, $niveau, $categorie);
    }

    public static function agent(?int $id, string $titre, string $message, ?string $lien = null, string $niveau = 'info', string $categorie = 'systeme'): void
    {
        self::envoyer('agent', $id, $titre, $message, $lien, $niveau, $categorie);
    }

    /** Un service OU une équipe (les deux sont des lignes de la table services). */
    public static function service(?int $id, string $titre, string $message, ?string $lien = null, string $niveau = 'info', string $categorie = 'systeme'): void
    {
        self::envoyer('service', $id, $titre, $message, $lien, $niveau, $categorie);
    }

    /** Identités connectées dans la session courante : [[type, id], …]. */
    public static function identites(): array
    {
        $liste = [];

        if (session()->has('citoyen_id')) {
            $liste[] = ['citoyen', (int) session('citoyen_id')];
        }
        if (session()->has('agent_id')) {
            $liste[] = ['agent', (int) session('agent_id')];
        }
        if (session()->has('equipe_id')) {
            $liste[] = ['service', (int) session('equipe_id')];
        }
        if (session()->has('service_id')) {
            $liste[] = ['service', (int) session('service_id')];
        }

        return $liste;
    }

    /** Notifications destinées à la session courante (aucune si personne n'est connecté). */
    public static function requete()
    {
        $identites = self::identites();
        $requete   = NotificationApp::query();

        if (! $identites) {
            return $requete->whereRaw('1 = 0');
        }

        return $requete->where(function ($w) use ($identites) {
            foreach ($identites as [$type, $id]) {
                $w->orWhere(fn ($x) => $x->where('destinataire_type', $type)->where('destinataire_id', $id));
            }
        });
    }

    /** Page d'accueil de la session courante (bouton « retour »). */
    public static function accueil(): string
    {
        return match (true) {
            session()->has('citoyen_id') => route('compteController'),
            session()->has('service_id') => route('services.compte'),
            session()->has('equipe_id')  => route('equipe.dashboard'),
            session()->has('agent_id')   => route('agent.espace'),
            default                      => url('/'),
        };
    }
}
