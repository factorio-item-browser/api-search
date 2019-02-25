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
     * Cleans already invalidated data from the cache.
     */
    public function cleanCache(): void;

    /**
     * Completely clears the cache from all results.
     */
    public function clearCache(): void;
}
