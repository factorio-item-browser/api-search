<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;

/**
 * The manager of the data fetchers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class FetcherService
{
    /**
     * The data fetchers.
     * @var array|FetcherInterface[]
     */
    protected $fetchers;

    /**
     * Initializes the service.
     * @param array|FetcherInterface[] $apiSearchFetchers
     */
    public function __construct(array $apiSearchFetchers)
    {
        $this->fetchers = $apiSearchFetchers;
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
