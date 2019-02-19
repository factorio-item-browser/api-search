<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManager;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;

/**
 * The main manager of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SearchManager implements SearchManagerInterface
{
    /**
     * The cached search result service.
     * @var CachedSearchResultService
     */
    protected $cachedSearchResultService;

    /**
     * The fetcher manager.
     * @var FetcherManager
     */
    protected $fetcherManager;

    /**
     * The query parser.
     * @var QueryParser
     */
    protected $queryParser;

    /**
     * Initializes the manager.
     * @param CachedSearchResultService $cachedSearchResultService
     * @param FetcherManager $fetcherManager
     * @param QueryParser $queryParser
     */
    public function __construct(
        CachedSearchResultService $cachedSearchResultService,
        FetcherManager $fetcherManager,
        QueryParser $queryParser
    ) {
        $this->cachedSearchResultService = $cachedSearchResultService;
        $this->fetcherManager = $fetcherManager;
        $this->queryParser = $queryParser;
    }

    /**
     * Parses the query string to an actual query entity.
     * @param string $queryString
     * @param array|int[] $modCombinationIds
     * @return Query
     */
    public function parseQuery(string $queryString, array $modCombinationIds): Query
    {
        return $this->queryParser->parse($queryString, $modCombinationIds);
    }

    /**
     * Searches for results using the query.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    public function search(Query $query): PaginatedResultCollection
    {
        $result = $this->cachedSearchResultService->getResults($query);
        if ($result ===  null) {
            $result = $this->executeQuery($query);
            $this->cachedSearchResultService->persistResults($query, $result);
        }
        return $result;
    }

    /**
     * Actually executes the query to search for results.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    protected function executeQuery(Query $query): PaginatedResultCollection
    {
        $searchResults = new AggregatingResultCollection();
        $this->fetcherManager->fetch($query, $searchResults);
        return $this->createPaginatedCollection($searchResults);
    }

    /**
     * Converts the search results to a paginated collection.
     * @param AggregatingResultCollection $searchResults
     * @return PaginatedResultCollection
     */
    protected function createPaginatedCollection(AggregatingResultCollection $searchResults): PaginatedResultCollection
    {
        $result = new PaginatedResultCollection();
        foreach ($searchResults->getMergedResults() as $searchResult) {
            $result->add($searchResult);
        }
        return $result;
    }
}
