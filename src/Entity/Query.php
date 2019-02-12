<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

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
     * The terms of the query.
     * @var array|Term[][]
     */
    protected $terms = [];

    /**
     * The hash of the parsed search query.
     * @var string
     */
    protected $hash = '';

    /**
     * Initializes the query.
     * @param string $queryString
     */
    public function __construct(string $queryString)
    {
        $this->queryString = $queryString;
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
     * Adds a term to the query.
     * @param Term $term
     * @return Query
     */
    public function addTerm(Term $term): self
    {
        $this->terms[$term->getType()][] = $term;
        return $this;
    }

    /**
     * Returns all terms.
     * @return array|Term[]
     */
    public function getTerms(): array
    {
        $result = [];
        if (count($this->terms) > 0) {
            $result = array_merge(...array_values($this->terms));
        }
        return $result;
    }

    /**
     * Returns the terms with the specified type.
     * @param string $type
     * @return array|Term[]
     */
    public function getTermsByType(string $type): array
    {
        return $this->terms[$type] ?? [];
    }

    /**
     * Returns all terms with any of the specified types.
     * @param array|string[] $types
     * @return array|Term[]
     */
    public function getTermsByTypes(array $types): array
    {
        $result = [];
        foreach ($types as $type) {
            $result = array_merge($result, $this->terms[$type] ?? []);
        }
        return $result;
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
