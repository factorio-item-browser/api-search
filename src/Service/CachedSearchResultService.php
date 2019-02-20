<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use DateTime;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Constant\Config;
use FactorioItemBrowser\Api\Search\Entity\Query;

/**
 * The service for the cached search results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultService
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
     * Initializes the service.
     * @param CachedSearchResultRepository $cachedSearchResultRepository
     * @param SerializerService $serializerService
     */
    public function __construct(
        CachedSearchResultRepository $cachedSearchResultRepository,
        SerializerService $serializerService
    ) {
        $this->cachedSearchResultRepository = $cachedSearchResultRepository;
        $this->serializerService = $serializerService;
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
            $maxAge = new DateTime(Config::MAX_CACHE_AGE);

            $entities = $this->cachedSearchResultRepository->findByHashes([$hash], $maxAge);
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
}
