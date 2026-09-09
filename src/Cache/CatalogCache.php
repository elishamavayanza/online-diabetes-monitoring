<?php

namespace App\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Accès simplifié au cache applicatif des données de catalogue
 * quasi-statiques (aliments, insulines, etc.).
 *
 * Les valeurs stockées sont les DTOs de réponse sérialisées : le cache ne
 * contient jamais d'entités Doctrine (pas de risque d'état périmé en mémoire).
 */
final class CatalogCache
{
    public function __construct(private readonly CacheItemPoolInterface $pool)
    {
    }

    /**
     * Lit un catalogue, ou le construit via $factory en cas d'absence.
     *
     * @param string             $key     Identifiant stable du catalogue.
     * @param callable(): mixed  $factory Fabrique des DTOs (jamais d'entité).
     *
     * @return mixed Valeur désérialisée (typiquement un tableau de DTOs).
     */
    public function get(string $key, callable $factory): mixed
    {
        $item = $this->pool->getItem('catalog.' . $key);
        if ($item->isHit()) {
            $data = $item->get();
            if (is_string($data)) {
                return unserialize($data, ['allowed_classes' => true]);
            }

            return $data;
        }

        $data = $factory();
        $item->set(serialize($data));
        $this->pool->save($item);

        return $data;
    }

    /**
     * Invalide un catalogue (appelé lors des créations/mises à jour/
     * suppressions de l'entité concernée).
     */
    public function evict(string $key): void
    {
        $this->pool->deleteItem('catalog.' . $key);
    }
}