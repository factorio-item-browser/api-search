<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

use FactorioItemBrowser\Api\Search\Collection\TermCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing a search query.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Query
{
    /**
     * The id of the combination to use.
     * @var UuidInterface
     */
    protected $combinationId;

    /**
     * The locale to prefer in translations.
     * @var string
     */
    protected $locale;

    /**
     * The unparsed query string.
     * @var string
     */
    protected $queryString;

    /**
     * The terms of the query.
     * @var TermCollection
     */
    protected $terms;

    /**
     * The hash of the parsed search query.
     * @var UuidInterface
     */
    protected $hash;

    /**
     * Initializes the query.
     */
    public function __construct()
    {
        $this->terms = new TermCollection();
    }

    /**
     * Sets the id of the combination to use.
     * @param UuidInterface $combinationId
     * @return $this
     */
    public function setCombinationId(UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    /**
     * Returns the id of the combination to use.
     * @return UuidInterface
     */
    public function getCombinationId(): UuidInterface
    {
        return $this->combinationId;
    }

    /**
     * Sets the locale to prefer in translations.
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the locale to prefer in translations.
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
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
     * @param UuidInterface $hash
     * @return $this
     */
    public function setHash(?UuidInterface $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Returns the hash of the parsed search query.
     * @return UuidInterface
     */
    public function getHash(): ?UuidInterface
    {
        return $this->hash;
    }
}
