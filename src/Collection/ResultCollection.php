<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;

/**
 * The collection holding a concrete type of results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TResult of ResultInterface
 */
class ResultCollection
{
    /** @var array<string|int, TResult> */
    private array $results = [];

    /**
     * Adds a result to the collection.
     * @param TResult $result
     * @return $this
     */
    public function add(ResultInterface $result): self
    {
        if ($result->getName() === '') {
            $this->results[] = $result;
        } else {
            $key = "{$result->getType()}|{$result->getName()}";
            if (isset($this->results[$key])) {
                $this->results[$key]->merge($result);
            } else {
                $this->results[$key] = $result;
            }
        }
        return $this;
    }

    /**
     * Removes a result from the collection.
     * @param TResult $result
     * @return $this
     */
    public function remove(ResultInterface $result): self
    {
        unset($this->results["{$result->getType()}|{$result->getName()}"]);
        return $this;
    }

    /**
     * Returns all results from the collection as simple array.
     * @return array<TResult>
     */
    public function getAll(): array
    {
        return array_values($this->results);
    }
}
