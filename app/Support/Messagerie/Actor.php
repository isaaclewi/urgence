<?php

namespace App\Support\Messagerie;

/**
 * Celui qui utilise la messagerie : un agent de l'administration, un service ou un citoyen.
 * Il est déduit du portail (préfixe du nom de route) et de la session existante.
 */
final class Actor
{
    public const ADMIN   = 'agent';
    public const SERVICE = 'service';
    public const CITOYEN = 'citoyen';

    public function __construct(
        public readonly string $portal,   // admin | services | citoyen
        public readonly string $type,     // agent | service | citoyen
        public readonly int $id,
    ) {}

    public static function fromPortal(string $portal): ?self
    {
        $map = [
            'admin'    => [self::ADMIN,   'admin_id'],
            'services' => [self::SERVICE, 'service_id'],
            'citoyen'  => [self::CITOYEN, 'citoyen_id'],
        ];

        if (! isset($map[$portal])) {
            return null;
        }

        [$type, $cle] = $map[$portal];
        $id = session($cle);

        return $id ? new self($portal, $type, (int) $id) : null;
    }

    public function isAdmin(): bool   { return $this->type === self::ADMIN; }
    public function isService(): bool { return $this->type === self::SERVICE; }
    public function isCitoyen(): bool { return $this->type === self::CITOYEN; }

    /** Préfixe des noms de routes de ce portail : « admin.messagerie. » etc. */
    public function routePrefix(): string
    {
        return $this->portal . '.messagerie.';
    }

    public function owns(object $message): bool
    {
        return $message->sender_type === $this->type && (int) $message->sender_id === $this->id;
    }
}
