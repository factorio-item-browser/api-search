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
    private UuidInterface $combinationId;
    private string $locale;
    private string $queryString;
    private TermCollection $terms;
    private UuidInterface $hash;

    public function __construct()
    {
        $this->terms = new TermCollection();
    }

    public function setCombinationId(UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    public function getCombinationId(): UuidInterface
    {
        return $this->combinationId;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getTerms(): TermCollection
    {
        return $this->terms;
    }

    public function setHash(UuidInterface $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getHash(): UuidInterface
    {
        return $this->hash;
    }
}
