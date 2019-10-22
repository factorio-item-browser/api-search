<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Parser;

use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The class parsing the query string into an actual query instance.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class QueryParser
{
    /**
     * Parses the query string into an actual query.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param string $queryString
     * @return Query
     */
    public function parse(UuidInterface $combinationId, string $locale, string $queryString): Query
    {
        $query = $this->createQuery($combinationId, $locale, $queryString);
        $this->parseQueryString($queryString, $query);
        $query->setHash($this->calculateHash($query));
        return $query;
    }

    /**
     * Creates a new query instance.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param string $queryString
     * @return Query
     */
    protected function createQuery(UuidInterface $combinationId, string $locale, string $queryString): Query
    {
        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($queryString);
        return $query;
    }

    /**
     * Actually parses the query string.
     * @param string $queryString
     * @param Query $query
     */
    protected function parseQueryString(string $queryString, Query $query): void
    {
        foreach (explode(' ', $queryString) as $keyword) {
            $keyword = strtolower(trim($keyword));
            if (strlen($keyword) >= 2) {
                $query->addTerm($this->createTerm(TermType::GENERIC, $keyword));
            }
        }
    }

    /**
     * Creates a new term instance.
     * @param string $type
     * @param string $value
     * @return Term
     */
    protected function createTerm(string $type, string $value): Term
    {
        return new Term($type, $value);
    }

    /**
     * Calculates the hash of the specified query.
     * @param Query $query
     * @return UuidInterface
     */
    protected function calculateHash(Query $query): UuidInterface
    {
        $hash = hash('md5', (string) json_encode($this->extractQueryData($query)));
        return Uuid::fromString($hash);
    }

    /**
     * Extracts the data from the query.
     * @param Query $query
     * @return array
     */
    protected function extractQueryData(Query $query): array
    {
        $result = [];
        foreach ($query->getTerms() as $term) {
            $result[] = implode('|', [$term->getType(), $term->getValue()]);
        }
        sort($result);
        return $result;
    }
}
