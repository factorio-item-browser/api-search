<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

/**
 * The interface for clearing the search cache.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SearchCacheClearInterface
{
    /**
     * Clears already expired data from the cache.
     */
    public function clearExpiredResults(): void;

    /**
     * Completely clears the cache from all results.
     */
    public function clearAll(): void;
}
