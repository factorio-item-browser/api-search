<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\SearchCacheClearInterface;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;

/**
 * The service for the cached search results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultService implements SearchCacheClearInterface
{
    private CachedSearchResultRepository $cachedSearchResultRepository;
    private SerializerService $serializerService;
    private DateTimeInterface $maxCacheAge;

    /**
     * @param CachedSearchResultRepository $cachedSearchResultRepository
     * @param SerializerService $serializerService
     * @param string $apiSearchMaxCacheAge
     * @throws Exception
     */
    public function __construct(
        CachedSearchResultRepository $cachedSearchResultRepository,
        SerializerService $serializerService,
        string $apiSearchMaxCacheAge
    ) {
        $this->cachedSearchResultRepository = $cachedSearchResultRepository;
        $this->serializerService = $serializerService;
        $this->maxCacheAge = new DateTimeImmutable($apiSearchMaxCacheAge);
    }

    /**
     * Returns the cached search results for the query, if available.
     * @param Query $query
     * @return PaginatedResultCollection|null
     */
    public function getResults(Query $query): ?PaginatedResultCollection
    {
        try {
            $entity = $this->cachedSearchResultRepository->find(
                $query->getCombinationId(),
                $query->getLocale(),
                $query->getHash(),
            );
            if ($entity === null) {
                return null;
            }

            $this->cachedSearchResultRepository->persist($entity); // Update lastSearchTime
            return $this->serializerService->unserialize($entity->getResultData());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Persists the search results for the query into the cache.
     * @param Query $query
     * @param PaginatedResultCollection $searchResults
     */
    public function persistResults(Query $query, PaginatedResultCollection $searchResults): void
    {
        try {
            $entity = new CachedSearchResult();
            $entity->setCombinationId($query->getCombinationId())
                   ->setLocale($query->getLocale())
                   ->setSearchQuery($query->getQueryString())
                   ->setSearchHash($query->getHash())
                   ->setResultData($this->serializerService->serialize($searchResults));

            $this->cachedSearchResultRepository->persist($entity);
        } catch (Exception $e) {
            // Silently ignore any cache errors.
        }
    }

    /**
     * Clears already expired data from the cache.
     */
    public function clearExpiredResults(): void
    {
        $this->cachedSearchResultRepository->clearExpiredResults($this->maxCacheAge);
    }

    /**
     * Completely clears the cache from all results.
     */
    public function clearAll(): void
    {
        $this->cachedSearchResultRepository->clearAll();
    }
}
