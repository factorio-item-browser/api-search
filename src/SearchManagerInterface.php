<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use Ramsey\Uuid\UuidInterface;

/**
 * The interface of the main manager of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SearchManagerInterface
{
    /**
     * Parses the query string to an actual query entity.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param string $queryString
     * @return Query
     */
    public function parseQuery(UuidInterface $combinationId, string $locale, string $queryString): Query;

    /**
     * Searches for results using the query.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    public function search(Query $query): PaginatedResultCollection;
}
