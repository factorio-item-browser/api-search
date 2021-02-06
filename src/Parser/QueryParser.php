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
    public function parse(UuidInterface $combinationId, string $locale, string $queryString): Query
    {
        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($queryString);

        $this->parseQueryString($queryString, $query);
        $query->setHash($this->calculateHash($query));
        return $query;
    }

    private function parseQueryString(string $queryString, Query $query): void
    {
        foreach (explode(' ', $queryString) as $keyword) {
            $keyword = strtolower(trim($keyword));
            if (strlen($keyword) >= 2) {
                $query->getTerms()->add(new Term(TermType::GENERIC, $keyword));
            }
        }
    }

    private function calculateHash(Query $query): UuidInterface
    {
        $data = [];
        foreach ($query->getTerms()->getAll() as $term) {
            $data[] = implode('|', [$term->getType(), $term->getValue()]);
        }
        sort($data);

        $hash = hash('md5', (string) json_encode($data));
        return Uuid::fromString($hash);
    }
}
