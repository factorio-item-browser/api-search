<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Term;
use FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ItemFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher
 */
class ItemFetcherTest extends TestCase
{
    /** @var ItemRepository&MockObject */
    private ItemRepository $itemRepository;
    /** @var MapperManagerInterface&MockObject */
    private MapperManagerInterface $mapperManager;

    protected function setUp(): void
    {
        $this->itemRepository = $this->createMock(ItemRepository::class);
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return ItemFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): ItemFetcher
    {
        return $this->getMockBuilder(ItemFetcher::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->itemRepository,
                        $this->mapperManager,
                    ])
                    ->getMock();
    }

    public function testFetch(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $item1 = $this->createMock(Item::class);
        $item2 = $this->createMock(Item::class);

        $query = new Query();
        $query->setCombinationId($combinationId);
        $query->getTerms()->add(new Term(TermType::GENERIC, 'abc'));

        $itemResult1 = $this->createMock(ItemResult::class);
        $itemResult1->expects($this->once())
                    ->method('setPriority')
                    ->with($this->identicalTo(SearchResultPriority::EXACT_MATCH));

        $itemResult2 = $this->createMock(ItemResult::class);
        $itemResult2->expects($this->once())
                    ->method('setPriority')
                    ->with($this->identicalTo(SearchResultPriority::EXACT_MATCH));

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addItem')
                      ->withConsecutive(
                          [$this->identicalTo($itemResult1)],
                          [$this->identicalTo($itemResult2)],
                      );

        $this->itemRepository->expects($this->once())
                             ->method('findByKeywords')
                             ->with($this->identicalTo($combinationId), $this->equalTo(['abc']))
                             ->willReturn([$item1, $item2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($item1), $this->isInstanceOf(ItemResult::class)],
                                [$this->identicalTo($item2), $this->isInstanceOf(ItemResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $itemResult1,
                                $itemResult2
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);
    }
}
