<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;

/**
 * The manager of the data fetchers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class FetcherManager
{
    /**
     * The data fetchers.
     * @var array|FetcherInterface[]
     */
    protected $fetchers;

    /**
     * Initializes the manager.
     * @param array|FetcherInterface[] $fetchers
     */
    public function __construct(array $fetchers)
    {
        $this->fetchers = $fetchers;
    }

    /**
     * Fetches the data to the query into the search results.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        foreach ($this->fetchers as $fetcher) {
            $fetcher->fetch($query, $searchResults);
        }
    }
}
