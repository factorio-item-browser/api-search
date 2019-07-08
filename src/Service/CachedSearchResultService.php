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
     * @param string $apiKeyMaxCacheAge
     * @throws Exception
     */
    public function __construct(
        CachedSearchResultRepository $cachedSearchResultRepository,
        SerializerService $serializerService,
        string $apiKeyMaxCacheAge
    ) {
        $this->cachedSearchResultRepository = $cachedSearchResultRepository;
        $this->serializerService = $serializerService;
        $this->maxCacheAge = new DateTimeImmutable($apiKeyMaxCacheAge);
    }

    /**
     * Returns the cached search results for the query, if available.
     * @param Query $query
     * @return PaginatedResultCollection|null
     */
    public function getResults(Query $query): ?PaginatedResultCollection
    {
        $result = null;
        $serializedResult = $this->fetchSerializedResults($query->getHash());
        if (is_string($serializedResult)) {
            $result = $this->serializerService->unserialize($serializedResult);
        }
        return $result;
    }

    /**
     * Fetches the serialized search result for the hash.
     * @param string $hash
     * @return string|null
     */
    protected function fetchSerializedResults(string $hash): ?string
    {
        $result = null;
        try {
            $entities = $this->cachedSearchResultRepository->findByHashes([$hash], $this->maxCacheAge);
            $entity = array_shift($entities);
            if ($entity instanceof CachedSearchResult) {
                $result = $entity->getResultData();
            }
        } catch (Exception $e) {
            // Silently ignore any cache errors.
        }
        return $result;
    }

    /**
     * Persists the search results for the query into the cache.
     * @param Query $query
     * @param PaginatedResultCollection $searchResults
     */
    public function persistResults(Query $query, PaginatedResultCollection $searchResults): void
    {
        try {
            $entity = new CachedSearchResult($query->getHash());
            $entity->setResultData($this->serializerService->serialize($searchResults));

            $this->cachedSearchResultRepository->persist($entity);
        } catch (Exception $e) {
            // Silently ignore any cache errors.
        }
    }

    /**
     * Cleans already invalidated data from the cache.
     */
    public function cleanCache(): void
    {
        $this->cachedSearchResultRepository->cleanup($this->maxCacheAge);
    }

    /**
     * Completely clears the cache from all results.
     */
    public function clearCache(): void
    {
        $this->cachedSearchResultRepository->clear();
    }
}
