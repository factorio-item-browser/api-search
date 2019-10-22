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
    /**
     * The cached search result repository.
     * @var CachedSearchResultRepository
     */
    protected $cachedSearchResultRepository;

    /**
     * The serializer service.
     * @var SerializerService
     */
    protected $serializerService;

    /**
     * The maximal age of the cache entries.
     * @var DateTimeInterface
     */
    protected $maxCacheAge;

    /**
     * Initializes the service.
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
        $serializedResult = $this->fetchSerializedResults($query);
        if ($serializedResult === null) {
            return null;
        }
        return $this->serializerService->unserialize($serializedResult);
    }

    /**
     * Fetches the serialized search result for the hash.
     * @param Query $query
     * @return string|null
     */
    protected function fetchSerializedResults(Query $query): ?string
    {
        try {
            $entity = $this->cachedSearchResultRepository->find(
                $query->getCombinationId(),
                $query->getLocale(),
                $query->getHash()
            );
            if ($entity === null || $entity->getLastSearchTime()->getTimestamp() < $this->maxCacheAge->getTimestamp()) {
                return null;
            }

            return $entity->getResultData();
        } catch (Exception $e) {
            // Silently ignore any cache errors.
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
