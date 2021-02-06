<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;

/**
 * The collection able to split results to different pages without manipulating their order.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class PaginatedResultCollection
{
    /** @var array<ResultInterface> */
    protected array $results = [];

    /**
     * @param ResultInterface $result
     * @return PaginatedResultCollection
     */
    public function add(ResultInterface $result): self
    {
        $this->results[] = $result;
        return $this;
    }

    public function count(): int
    {
        return count($this->results);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array<ResultInterface>
     */
    public function getResults(int $offset, int $limit): array
    {
        return array_values(array_slice($this->results, $offset, $limit));
    }
}
