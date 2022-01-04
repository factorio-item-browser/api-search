<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

use BluePsyduck\LaminasAutoWireFactory\Attribute\InjectAliasArray;
use BluePsyduck\LaminasAutoWireFactory\Attribute\ReadConfig;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use Ramsey\Uuid\UuidInterface;

/**
 * The main manager of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SearchManager implements SearchManagerInterface
{
    /**
     * @param array<FetcherInterface> $fetchers
     */
    public function __construct(
        private readonly CachedSearchResultService $cachedSearchResultService,
        private readonly QueryParser $queryParser,
        #[InjectAliasArray(ConfigKey::MAIN, ConfigKey::FETCHERS)]
        private readonly array $fetchers,
        #[ReadConfig(ConfigKey::MAIN, ConfigKey::MAX_SEARCH_RESULTS)]
        private readonly int $maxSearchResults
    ) {
    }

    /**
     * Parses the query string to an actual query entity.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param string $queryString
     * @return Query
     */
    public function parseQuery(UuidInterface $combinationId, string $locale, string $queryString): Query
    {
        return $this->queryParser->parse($combinationId, $locale, $queryString);
    }

    /**
     * Searches for results using the query.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    public function search(Query $query): PaginatedResultCollection
    {
        $paginatedResults = $this->cachedSearchResultService->getResults($query);
        if ($paginatedResults !== null) {
            $paginatedResults->setIsCached(true);
            return $paginatedResults;
        }

        $paginatedResults = $this->executeQuery($query);
        $this->cachedSearchResultService->persistResults($query, $paginatedResults);
        return $paginatedResults;
    }

    /**
     * Executes the query to search for results.
     * @param Query $query
     * @return PaginatedResultCollection
     */
    private function executeQuery(Query $query): PaginatedResultCollection
    {
        $searchResults = new AggregatingResultCollection();
        foreach ($this->fetchers as $fetcher) {
            $fetcher->fetch($query, $searchResults);
        }

        $paginatedResults = new PaginatedResultCollection();
        foreach (array_slice($searchResults->getMergedResults(), 0, $this->maxSearchResults) as $searchResult) {
            $paginatedResults->add($searchResult);
        }
        return $paginatedResults;
    }
}
