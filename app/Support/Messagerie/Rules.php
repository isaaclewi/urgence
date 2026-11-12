<?php

namespace App\Support\Messagerie;

use App\Models\DiscussionMember;
use App\Models\DiscussionMessage;
use App\Models\DiscussionSpace;

/**
 * Toutes les règles d'accès de la messagerie, au même endroit.
 *
 *  - Administration : voit et administre tous les groupes (y compris archivés).
 *  - Service        : n'accède à un groupe que si l'administration l'y a autorisé
 *                     (ligne dans discussion_members) ; ne voit jamais les groupes archivés.
 *  - Citoyen        : accède aux groupes « public » actifs ; écrit seulement si le groupe le permet.
 */
final class Rules
{
    /** Délai pendant lequel l'auteur peut modifier son message (minutes). */
    public const EDIT_WINDOW_MINUTES = 15;

    /** @var array<string, DiscussionMember|null> */
    private static array $cache = [];

    public static function member(Actor $actor, DiscussionSpace $space): ?DiscussionMember
    {
        $cle = $space->id . ':' . $actor->type . ':' . $actor->id;

        if (! array_key_exists($cle, self::$cache)) {
            self::$cache[$cle] = DiscussionMember::where('discussion_space_id', $space->id)
                ->where('member_type', $actor->type)
                ->where('member_id', $actor->id)
                ->first();
        }

        return self::$cache[$cle];
    }

    public static function forget(): void
    {
        self::$cache = [];
    }

    public static function canView(Actor $actor, DiscussionSpace $space): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if (! $space->is_active) {
            return false;
        }

        $membre = self::member($actor, $space);
        $actif  = $membre && in_array($membre->status, ['active', 'muted'], true);

        if ($actor->isService()) {
            return $actif;
        }

        // Citoyen : groupes publics, ou groupe privé où il a été ajouté
        return $space->type === 'public' || $actif;
    }

    public static function canPost(Actor $actor, DiscussionSpace $space): bool
    {
        if (! $space->is_active || ! self::canView($actor, $space)) {
            return false;
        }

        if ($actor->isAdmin()) {
            return true;
        }

        $membre = self::member($actor, $space);

        if ($actor->isService()) {
            if (! $membre || $membre->status !== 'active' || ! $membre->can_post) {
                return false;
            }

            return $space->services_can_post || $membre->isModerator();
        }

        return $space->citizens_can_post && (! $membre || $membre->status !== 'muted');
    }

    public static function isModerator(Actor $actor, DiscussionSpace $space): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if (! $actor->isService()) {
            return false;
        }

        $membre = self::member($actor, $space);

        return (bool) ($membre && $membre->status === 'active' && $membre->isModerator());
    }

    public static function canEdit(Actor $actor, DiscussionMessage $m): bool
    {
        return ! $m->is_deleted
            && $actor->owns($m)
            && $m->created_at
            && $m->created_at->gt(now()->subMinutes(self::EDIT_WINDOW_MINUTES));
    }

    public static function canDelete(Actor $actor, DiscussionSpace $space, DiscussionMessage $m): bool
    {
        if ($m->is_deleted) {
            return false;
        }

        return $actor->owns($m) || self::isModerator($actor, $space);
    }
}
