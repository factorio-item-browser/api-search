<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Term;

/**
 * The collection of terms.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TermCollection
{
    /** @var array<string, array<Term>> */
    private array $termsByType = [];

    public function add(Term $term): self
    {
        $this->termsByType[$term->getType()][] = $term;
        return $this;
    }

    /**
     * @return array<Term>
     */
    public function getAll(): array
    {
        $result = [];
        if (count($this->termsByType) > 0) {
            $result = array_merge(...array_values($this->termsByType));
        }
        return $result;
    }

    /**
     * @return array<string>
     */
    public function getAllValues(): array
    {
        return $this->extractValuesFromTerms($this->getAll());
    }

    /**
     * @param array<string> $types
     * @return array<Term>
     */
    public function getByTypes(array $types): array
    {
        $result = [];
        foreach ($types as $type) {
            $result = array_merge($result, $this->termsByType[$type] ?? []);
        }
        return $result;
    }

    /**
     * @param array<string> $types
     * @return array<string>
     */
    public function getValuesByTypes(array $types): array
    {
        return $this->extractValuesFromTerms($this->getByTypes($types));
    }

    /**
     * @param array<Term> $terms
     * @return array<string>
     */
    private function extractValuesFromTerms(array $terms): array
    {
        return array_values(array_unique(array_map(fn(Term $term): string => $term->getValue(), $terms)));
    }
}
