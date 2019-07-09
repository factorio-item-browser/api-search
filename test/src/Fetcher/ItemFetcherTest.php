<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\TestHelper\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ItemFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher
 */
class ItemFetcherTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked item repository.
     * @var ItemRepository&MockObject
     */
    protected $itemRepository;

    /**
     * The mocked mapper manager.
     * @var MapperManagerInterface&MockObject
     */
    protected $mapperManager;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->itemRepository = $this->createMock(ItemRepository::class);
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $fetcher = new ItemFetcher($this->itemRepository, $this->mapperManager);

        $this->assertSame($this->itemRepository, $this->extractProperty($fetcher, 'itemRepository'));
        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
    }

    /**
     * Tests the fetch method.
     * @throws MapperException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var Item&MockObject $item1 */
        $item1 = $this->createMock(Item::class);
        /* @var Item&MockObject $item2 */
        $item2 = $this->createMock(Item::class);
        /* @var ItemResult&MockObject $itemResult1 */
        $itemResult1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $itemResult2 */
        $itemResult2 = $this->createMock(ItemResult::class);

        $items = [$item1, $item2];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addItem')
                      ->withConsecutive(
                          [$this->identicalTo($itemResult1)],
                          [$this->identicalTo($itemResult2)]
                      );

        /* @var ItemFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ItemFetcher::class)
                        ->setMethods(['fetchItems', 'mapItem'])
                        ->setConstructorArgs([$this->itemRepository, $this->mapperManager])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('fetchItems')
                ->with($this->identicalTo($query))
                ->willReturn($items);

        $fetcher->expects($this->exactly(2))
                ->method('mapItem')
                ->withConsecutive(
                    [$this->identicalTo($item1)],
                    [$this->identicalTo($item2)]
                )
                ->willReturnOnConsecutiveCalls(
                    $itemResult1,
                    $itemResult2
                );

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the fetchItems method.
     * @throws ReflectionException
     * @covers ::fetchItems
     */
    public function testFetchItems(): void
    {
        $keywords = ['abc', 'def'];
        $modCombinationIds = [42, 1337];

        $items = [
            $this->createMock(Item::class),
            $this->createMock(Item::class),
        ];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getTermValuesByType')
              ->with($this->identicalTo(TermType::GENERIC))
              ->willReturn($keywords);
        $query->expects($this->once())
              ->method($this->identicalTo('getModCombinationIds'))
              ->willReturn($modCombinationIds);

        $this->itemRepository->expects($this->once())
                             ->method('findByKeywords')
                             ->with($this->identicalTo($keywords), $this->identicalTo($modCombinationIds))
                             ->willReturn($items);

        $fetcher = new ItemFetcher($this->itemRepository, $this->mapperManager);
        $result = $this->invokeMethod($fetcher, 'fetchItems', $query);

        $this->assertSame($items, $result);
    }

    /**
     * Tests the mapItem method.
     * @throws ReflectionException
     * @covers ::mapItem
     */
    public function testMapItem(): void
    {
        /* @var Item&MockObject $item */
        $item = $this->createMock(Item::class);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($item), $this->isInstanceOf(ItemResult::class));

        $fetcher = new ItemFetcher($this->itemRepository, $this->mapperManager);
        $result = $this->invokeMethod($fetcher, 'mapItem', $item);

        $this->assertSame(SearchResultPriority::EXACT_MATCH, $result->getPriority());
    }
}
