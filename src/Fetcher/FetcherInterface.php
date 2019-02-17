<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;

/**
 * The interface of the data fetchers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface FetcherInterface
{
    /**
     * Fetches the data matching the specified query.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void;
}
