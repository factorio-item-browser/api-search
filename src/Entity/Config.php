<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

/**
 * The class representing the config of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Config
{
    /**
     * The maximal number of search results to return.
     * @var int
     */
    protected $maxSearchResults = 0;

    /**
     * The maximal age of the cache entries.
     * @var DateTimeInterface
     */
    protected $maxCacheAge;

    /**
     * The container aliases of the fetchers to use.
     * @var array|string[]
     */
    protected $fetcherAliases = [];

    /**
     * The container aliases of the serializers to use.
     * @var array|string[]
     */
    protected $serializerAliases = [];

    /**
     * Initializes the entity.
     * @throws Exception
     */
    public function __construct()
    {
        $this->maxCacheAge = new DateTimeImmutable();
    }

    /**
     * Sets the maximal number of search results to return.
     * @param int $maxSearchResults
     * @return $this
     */
    public function setMaxSearchResults(int $maxSearchResults): self
    {
        $this->maxSearchResults = $maxSearchResults;
        return $this;
    }

    /**
     * Returns the maximal number of search results to return.
     * @return int
     */
    public function getMaxSearchResults(): int
    {
        return $this->maxSearchResults;
    }

    /**
     * Sets the maximal age of the cache entries.
     * @param DateTimeInterface $maxCacheAge
     * @return $this
     */
    public function setMaxCacheAge(DateTimeInterface $maxCacheAge): self
    {
        $this->maxCacheAge = $maxCacheAge;
        return $this;
    }

    /**
     * Returns the maximal age of the cache entries.
     * @return DateTimeInterface
     */
    public function getMaxCacheAge(): DateTimeInterface
    {
        return $this->maxCacheAge;
    }

    /**
     * Sets the container aliases of the fetchers to use.
     * @param array|string[] $fetcherAliases
     * @return $this
     */
    public function setFetcherAliases(array $fetcherAliases): self
    {
        $this->fetcherAliases = $fetcherAliases;
        return $this;
    }

    /**
     * Returns the container aliases of the fetchers to use.
     * @return array|string[]
     */
    public function getFetcherAliases()
    {
        return $this->fetcherAliases;
    }

    /**
     * Sets the the container aliases of the serializers to use.
     * @param array|string[] $serializerAliases
     * @return $this
     */
    public function setSerializerAliases(array $serializerAliases): self
    {
        $this->serializerAliases = $serializerAliases;
        return $this;
    }

    /**
     * Returns the the container aliases of the serializers to use.
     * @return array|string[]
     */
    public function getSerializerAliases()
    {
        return $this->serializerAliases;
    }
}
