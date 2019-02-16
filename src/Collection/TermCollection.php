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
    /**
     * The terms grouped by their type.
     * @var array|Term[][]
     */
    protected $termsByType = [];

    /**
     * Adds a term to the collection.
     * @param Term $term
     * @return TermCollection
     */
    public function add(Term $term): self
    {
        $this->termsByType[$term->getType()][] = $term;
        return $this;
    }

    /**
     * Returns all terms of the collection.
     * @return array|Term[]
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
     * Returns the values of all terms.
     * @return array|string[]
     */
    public function getAllValues(): array
    {
        return $this->getValues($this->getAll());
    }

    /**
     * Returns the terms of the specified types.
     * @param array|string[] $types
     * @return array|Term[]
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
     * Returns the terms with the specified type.
     * @param string $type
     * @return array|Term[]
     */
    public function getByType(string $type): array
    {
        return $this->getByTypes([$type]);
    }

    /**
     * Returns the values of the terms with the specified types.
     * @param array|string[] $types
     * @return array|string[]
     */
    public function getValuesByTypes(array $types): array
    {
        return $this->getValues($this->getByTypes($types));
    }

    /**
     * Returns the values of the terms with the specified type.
     * @param string $type
     * @return array|string[]
     */
    public function getValuesByType(string $type): array
    {
        return $this->getValues($this->getByType($type));
    }

    /**
     * Returns the values of the specified terms.
     * @param array|Term[] $terms
     * @return array|string[]
     */
    protected function getValues(array $terms): array
    {
        $result = [];
        foreach ($terms as $term) {
            $result[] = $term->getValue();
        }
        return $result;
    }
}
