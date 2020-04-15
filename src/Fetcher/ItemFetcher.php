<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;

/**
 * The class fetching items matching the query.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemFetcher implements FetcherInterface
{
    /**
     * The repository of the items.
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * The mapper manager.
     * @var MapperManagerInterface
     */
    protected $mapperManager;

    /**
     * Initializes the data fetcher.
     * @param ItemRepository $itemRepository
     * @param MapperManagerInterface $mapperManager
     */
    public function __construct(ItemRepository $itemRepository, MapperManagerInterface $mapperManager)
    {
        $this->itemRepository = $itemRepository;
        $this->mapperManager = $mapperManager;
    }

    /**
     * Fetches the data matching the specified query.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     * @throws MapperException
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        foreach ($this->fetchItems($query) as $item) {
            $searchResults->addItem($this->mapItem($item));
        }
    }

    /**
     * Fetches the items matching the query.
     * @param Query $query
     * @return array|Item[]
     */
    protected function fetchItems(Query $query): array
    {
        return $this->itemRepository->findByKeywords(
            $query->getCombinationId(),
            $query->getTermValuesByType(TermType::GENERIC)
        );
    }

    /**
     * Maps the specified item to a result.
     * @param Item $item
     * @return ItemResult
     * @throws MapperException
     */
    protected function mapItem(Item $item): ItemResult
    {
        $result = new ItemResult();
        $this->mapperManager->map($item, $result);

        $result->setPriority(SearchResultPriority::EXACT_MATCH);
        return $result;
    }
}
