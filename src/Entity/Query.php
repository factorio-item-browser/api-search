<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

use FactorioItemBrowser\Api\Search\Collection\TermCollection;

/**
 * The class representing a search query.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Query
{
    /**
     * The unparsed query string.
     * @var string
     */
    protected $queryString;

    /**
     * The ids of the mod combinations to use.
     * @var array|int[]
     */
    protected $modCombinationIds;

    /**
     * The terms of the query.
     * @var TermCollection
     */
    protected $terms;

    /**
     * The hash of the parsed search query.
     * @var string
     */
    protected $hash = '';

    /**
     * Initializes the query.
     * @param string $queryString
     * @param array|int[] $modCombinationIds
     */
    public function __construct(string $queryString, array $modCombinationIds)
    {
        $this->queryString = $queryString;
        $this->modCombinationIds = $modCombinationIds;

        $this->terms = new TermCollection();
    }

    /**
     * Sets the unparsed query string.
     * @param string $queryString
     * @return $this
     */
    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * Returns the unparsed query string.
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Sets the ids of the mod combinations to use.
     * @param array|int[] $modCombinationIds
     * @return $this
     */
    public function setModCombinationIds(array $modCombinationIds): self
    {
        $this->modCombinationIds = $modCombinationIds;
        return $this;
    }

    /**
     * Returns the ids of the mod combinations to use.
     * @return array|int[]
     */
    public function getModCombinationIds(): array
    {
        return $this->modCombinationIds;
    }

    /**
     * Adds a term to the query.
     * @param Term $term
     * @return Query
     */
    public function addTerm(Term $term): self
    {
        $this->terms->add($term);
        return $this;
    }

    /**
     * Returns all terms.
     * @return array|Term[]
     */
    public function getTerms(): array
    {
        return $this->terms->getAll();
    }

    /**
     * Returns the values of all terms.
     * @return array|string[]
     */
    public function getTermValues(): array
    {
        return $this->terms->getAllValues();
    }

    /**
     * Returns the terms with the specified type.
     * @param string $type
     * @return array|Term[]
     */
    public function getTermsByType(string $type): array
    {
        return $this->terms->getByType($type);
    }

    /**
     * Returns all terms with any of the specified types.
     * @param array|string[] $types
     * @return array|Term[]
     */
    public function getTermsByTypes(array $types): array
    {
        return $this->terms->getByTypes($types);
    }

    /**
     * Returns the term values with the specified type.
     * @param string $type
     * @return array|string[]
     */
    public function getTermValuesByType(string $type): array
    {
        return $this->terms->getValuesByType($type);
    }

    /**
     * Returns the term values with any of the specified types.
     * @param array|string[] $types
     * @return array|string[]
     */
    public function getTermValuesByTypes(array $types): array
    {
        return $this->terms->getValuesByTypes($types);
    }

    /**
     * Sets the hash of the parsed search query.
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Returns the hash of the parsed search query.
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
