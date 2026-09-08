<?php

namespace App\Service\Common;

use App\Entity\Identity\User;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Présence "quasi temps réel" des utilisateurs, sans WebSocket.
 *
 * L'activité de chaque utilisateur est enregistrée dans le cache applicatif
 * (cache.app) par PresenceSubscriber à chaque requête API authentifiée.
 * Un utilisateur est considéré « en ligne » tant que son dernier souffle
 * d'activité remonte à moins de ONLINE_TTL secondes.
 */
class PresenceService
{
    /** Durée (en secondes) pendant laquelle une activité compte comme « en ligne ». */
    public const ONLINE_TTL = 90;

    private const KEY_PREFIX = 'presence.activity.';

    public function __construct(
        #[Autowire(service: 'cache.app')]
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * Enregistre un souffle d'activité pour l'utilisateur (rafraîchit la TTL).
     */
    public function recordActivity(User $user, ?DateTimeImmutable $now = null): void
    {
        $now ??= new DateTimeImmutable();

        $item = $this->cache->getItem($this->key($user));
        $item->expiresAfter(self::ONLINE_TTL);
        $item->set($now->format(\DateTimeInterface::ATOM));

        $this->cache->save($item);
    }

    /**
     * Indique si l'utilisateur est actuellement « en ligne ».
     */
    public function isOnline(User $user): bool
    {
        return $this->getLastSeenAt($user) !== null;
    }

    /**
     * Retourne la dernière activité connue, ou null si hors ligne/inconnue.
     */
    public function getLastSeenAt(User $user): ?DateTimeImmutable
    {
        $item = $this->cache->getItem($this->key($user));

        if (!$item->isHit()) {
            return null;
        }

        $value = $item->get();

        if (!is_string($value) || $value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $value);

        return $date === false ? null : $date;
    }

    private function key(User $user): string
    {
        return self::KEY_PREFIX . ((string) $user->getId());
    }
}