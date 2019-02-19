<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Constant;

/**
 * The interface holding some static config values.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface Config
{
    /**
     * The maximal number of search results to return.
     */
    public const MAX_SEARCH_RESULTS = 1000;

    /**
     * The maximal age of the search result cache.
     */
    public const MAX_CACHE_AGE = '-3600 seconds';
}
