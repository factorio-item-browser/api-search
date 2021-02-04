<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;

/**
 * The class fetching missing ids of already matched items.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MissingItemIdFetcher implements FetcherInterface
{
    private ItemRepository $itemRepository;
    private MapperManagerInterface $mapperManager;

    public function __construct(ItemRepository $itemRepository, MapperManagerInterface $mapperManager)
    {
        $this->itemRepository = $itemRepository;
        $this->mapperManager = $mapperManager;
    }

    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $namesByTypes = $this->getTypesAndNamesWithMissingId($searchResults);
        $items = $this->itemRepository->findByTypesAndNames($query->getCombinationId(), $namesByTypes);
        foreach ($items as $item) {
            $searchResults->addItem($this->mapperManager->map($item, new ItemResult()));
        }
    }

    private function getTypesAndNamesWithMissingId(AggregatingResultCollection $searchResults): NamesByTypes
    {
        $namesByTypes = new NamesByTypes();
        foreach ($searchResults->getItems() as $item) {
            if ($item->getId() === null) {
                $namesByTypes->addName($item->getType(), $item->getName());
            }
        }
        return $namesByTypes;
    }
}
