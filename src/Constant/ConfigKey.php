<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Constant;

/**
 * The interface holding the keys of the config.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ConfigKey
{
    /**
     * The key holding the name of the project.
     */
    public const PROJECT = 'factorio-item-browser';

    /**
     * The key holding the name of the library itself.
     */
    public const API_SEARCH = 'api-search';

    /**
     * The key for the maximal number of search results to return.
     */
    public const MAX_SEARCH_RESULTS = 'max-search-results';

    /**
     * The key for the maximal age of the cache entries.
     */
    public const MAX_CACHE_AGE = 'max-cache-age';

    /**
     * The key for the fetchers.
     */
    public const FETCHERS = 'fetchers';

    /**
     * The key for the serializers.
     */
    public const SERIALIZERS = 'serializers';
}
