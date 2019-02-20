<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\Common\Test\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the MissingItemIdFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher
 */
class MissingItemIdFetcherTest extends TestCase
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
     * @throws ReflectionException
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
        $fetcher = new MissingItemIdFetcher($this->itemRepository, $this->mapperManager);

        $this->assertSame($this->itemRepository, $this->extractProperty($fetcher, 'itemRepository'));
        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
    }

    /**
     * Tests the fetch method.
     * @throws ReflectionException
     * @throws MapperException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        $namesByTypes = ['abc' => ['def', 'ghi']];

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

        /* @var MissingItemIdFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(MissingItemIdFetcher::class)
                        ->setMethods(['getTypesAndNamesWithMissingIds', 'fetchItems', 'mapItem'])
                        ->setConstructorArgs([$this->itemRepository, $this->mapperManager])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('getTypesAndNamesWithMissingIds')
                ->with($this->identicalTo($searchResults))
                ->willReturn($namesByTypes);
        $fetcher->expects($this->once())
                ->method('fetchItems')
                ->with($this->identicalTo($namesByTypes), $this->identicalTo($query))
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
     * Tests the getTypesAndNamesWithMissingIds method.
     * @throws ReflectionException
     * @covers ::getTypesAndNamesWithMissingIds
     */
    public function testGetTypesAndNamesWithMissingIds(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->once())
              ->method('getId')
              ->willReturn(0);
        $item1->expects($this->once())
              ->method('getType')
              ->willReturn('abc');
        $item1->expects($this->once())
              ->method('getName')
              ->willReturn('def');

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->once())
              ->method('getId')
              ->willReturn(1337);

        /* @var ItemResult&MockObject $item3 */
        $item3 = $this->createMock(ItemResult::class);
        $item3->expects($this->once())
              ->method('getId')
              ->willReturn(0);
        $item3->expects($this->once())
              ->method('getType')
              ->willReturn('abc');
        $item3->expects($this->once())
              ->method('getName')
              ->willReturn('ghi');

        $items = [$item1, $item2, $item3];
        $expectedResult = [
            'abc' => ['def', 'ghi'],
        ];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getItems')
                      ->willReturn($items);

        $fetcher = new MissingItemIdFetcher($this->itemRepository, $this->mapperManager);
        $result = $this->invokeMethod($fetcher, 'getTypesAndNamesWithMissingIds', $searchResults);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the fetchItems method.
     * @throws ReflectionException
     * @covers ::fetchItems
     */
    public function testFetchItems(): void
    {
        $namesByTypes = [
            'abc' => ['def', 'ghi'],
        ];
        $modCombinationIds = [42, 1337];

        $items = [
            $this->createMock(Item::class),
            $this->createMock(Item::class),
        ];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method($this->identicalTo('getModCombinationIds'))
              ->willReturn($modCombinationIds);

        $this->itemRepository->expects($this->once())
                             ->method('findByTypesAndNames')
                             ->with($this->identicalTo($namesByTypes), $this->identicalTo($modCombinationIds))
                             ->willReturn($items);

        $fetcher = new MissingItemIdFetcher($this->itemRepository, $this->mapperManager);
        $result = $this->invokeMethod($fetcher, 'fetchItems', $namesByTypes, $query);

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

        $fetcher = new MissingItemIdFetcher($this->itemRepository, $this->mapperManager);
        $this->invokeMethod($fetcher, 'mapItem', $item);
    }
}
