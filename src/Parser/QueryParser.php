<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Parser;

use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;

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
     * @param string $queryString
     * @param array|int[] $modCombinationIds
     * @return Query
     */
    public function parse(string $queryString, array $modCombinationIds): Query
    {
        $result = $this->createQuery($queryString, $modCombinationIds);
        $this->parseQueryString($queryString, $result);
        $result->setHash($this->calculateHash($result));
        return $result;
    }

    /**
     * Creates a new query instance.
     * @param string $queryString
     * @param array|int[] $modCombinationIds
     * @return Query
     */
    protected function createQuery(string $queryString, array $modCombinationIds): Query
    {
        return new Query($queryString, $modCombinationIds);
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
     * @return string
     */
    protected function calculateHash(Query $query): string
    {
        $data = $this->extractQueryData($query);
        return substr(hash('md5', (string) json_encode([
            $data,
            $query->getModCombinationIds(),
        ])), 0, 16);
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
