<?php

declare(strict_types=1);

/**
 * The interface of the main manager of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;

/**
 * The main manager of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SearchManagerInterface
{
    /**
     * Parses the query string to an actual query entity.
     * @param string $queryString
     * @param array|int[] $modCombinationIds
     * @return Query
     */
    public function parseQuery(string $queryString, array $modCombinationIds): Query;

    /**
     * Searches for results using the query.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    public function search(Query $query): PaginatedResultCollection;
}
