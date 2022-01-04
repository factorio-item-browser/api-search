<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
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
    public function __construct(
        private readonly ItemRepository $itemRepository,
        private readonly MapperManagerInterface $mapperManager,
    ) {
    }

    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $items = $this->itemRepository->findByKeywords(
            $query->getCombinationId(),
            $query->getTerms()->getValuesByTypes([TermType::GENERIC]),
        );

        foreach ($items as $item) {
            $itemResult = $this->mapperManager->map($item, new ItemResult());
            $itemResult->setPriority(SearchResultPriority::EXACT_MATCH);

            $searchResults->addItem($itemResult);
        }
    }
}
