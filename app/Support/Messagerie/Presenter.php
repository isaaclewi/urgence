<?php

namespace App\Support\Messagerie;

use App\Models\Admins;
use App\Models\Citoyens;
use App\Models\Services;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Prépare l'affichage : qui écrit (nom, service, couleur) et le texte des messages.
 * Les noms sont chargés en groupe (3 requêtes au plus), pas message par message.
 */
final class Presenter
{
    /**
     * @param  iterable<object>  $messages
     * @return array<string, array{name:string, badge:string, tone:string, initials:string}>
     */
    public static function authors(iterable $messages): array
    {
        $ids = ['agent' => [], 'service' => [], 'citoyen' => []];

        foreach ($messages as $m) {
            if ($m && isset($ids[$m->sender_type])) {
                $ids[$m->sender_type][$m->sender_id] = true;
            }
        }

        $auteurs = [];

        if ($ids['agent']) {
            foreach (Admins::whereIn('id', array_keys($ids['agent']))->get(['id', 'nom', 'prenom']) as $a) {
                $nom = trim(($a->prenom ?? '') . ' ' . ($a->nom ?? '')) ?: 'Administration';
                $auteurs['agent:' . $a->id] = self::fiche($nom, 'Administration', 'admin');
            }
        }

        if ($ids['service']) {
            foreach (Services::whereIn('id', array_keys($ids['service']))->get(['id', 'nom', 'role']) as $s) {
                [$libelle, $ton] = self::roleService($s->role);
                $auteurs['service:' . $s->id] = self::fiche($s->nom ?: 'Service', $libelle, $ton);
            }
        }

        if ($ids['citoyen']) {
            foreach (Citoyens::whereIn('id', array_keys($ids['citoyen']))->get(['id', 'nom', 'prenom', 'pseudo']) as $c) {
                $nom = $c->pseudo ?: trim(($c->prenom ?? '') . ' ' . ($c->nom ?? '')) ?: 'Citoyen';
                $auteurs['citoyen:' . $c->id] = self::fiche($nom, 'Citoyen', 'citoyen');
            }
        }

        return $auteurs;
    }

    public static function author(array $auteurs, string $type, int $id): array
    {
        return $auteurs[$type . ':' . $id] ?? self::fiche('Compte supprimé', '', 'citoyen');
    }

    /** Libellé et couleur d'un type de service. */
    public static function roleService(?string $role): array
    {
        $r = Str::of((string) $role)->ascii()->lower()->toString();

        return match (true) {
            str_contains($r, 'hopital') || str_contains($r, 'sante') => ['Hôpital', 'hopital'],
            str_contains($r, 'police')                                => ['Police', 'police'],
            str_contains($r, 'pompier')                               => ['Pompiers', 'pompier'],
            default                                                   => [$role ? Str::title($role) : 'Service', 'service'],
        };
    }

    private static function fiche(string $nom, string $badge, string $ton): array
    {
        $mots = preg_split('/\s+/u', trim($nom)) ?: [$nom];
        $init = mb_strtoupper(mb_substr($mots[0] ?? '?', 0, 1) . (isset($mots[1]) ? mb_substr($mots[1], 0, 1) : ''));

        return ['name' => $nom, 'badge' => $badge, 'tone' => $ton, 'initials' => $init ?: '?'];
    }

    /** Texte du message prêt à afficher : échappé, liens cliquables, retours à la ligne. */
    public static function text(?string $texte): string
    {
        $html = e((string) $texte);

        $html = preg_replace_callback(
            '~(https?://[^\s<]+[^\s<.,;:!?)\]])~i',
            fn ($m) => '<a href="' . $m[1] . '" target="_blank" rel="noopener noreferrer">' . $m[1] . '</a>',
            $html
        );

        return nl2br($html, false);
    }

    /** Extrait court pour les aperçus (liste des groupes, citation de réponse). */
    public static function excerpt(?string $texte, int $max = 90): string
    {
        return Str::limit(trim(preg_replace('/\s+/u', ' ', (string) $texte)), $max);
    }

    /** Aperçu d'un message (liste, citation) : texte, ou type de pièce jointe. */
    public static function preview(?object $m, int $max = 90): string
    {
        if (! $m) {
            return '';
        }
        if ($m->is_deleted) {
            return 'Message supprimé';
        }
        if (filled($m->message)) {
            return self::excerpt($m->message, $max);
        }

        return match ($m->message_type) {
            'image' => 'Photo',
            'video' => 'Vidéo',
            default => 'Fichier : ' . ($m->file_name ?: 'pièce jointe'),
        };
    }

    public static function taille(?int $octets): string
    {
        $o = (int) $octets;

        return match (true) {
            $o >= 1048576 => number_format($o / 1048576, 1, ',', ' ') . ' Mo',
            $o >= 1024    => (int) round($o / 1024) . ' Ko',
            default       => $o . ' o',
        };
    }

    /**
     * Charge les messages cités (réponses) et renvoie [messages, auteurs] prêts pour les vues.
     */
    public static function prepare(Collection $messages): array
    {
        $messages->load('replyTo');

        $tous = $messages->concat($messages->pluck('replyTo')->filter());

        return [$messages, self::authors($tous)];
    }
}
