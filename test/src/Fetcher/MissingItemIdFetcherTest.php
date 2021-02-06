<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the MissingItemIdFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher
 */
class MissingItemIdFetcherTest extends TestCase
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
     * @return MissingItemIdFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): MissingItemIdFetcher
    {
        return $this->getMockBuilder(MissingItemIdFetcher::class)
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

        $itemResult1 = new ItemResult();
        $itemResult1->setType('abc')
                    ->setName('def');
        $itemResult2 = new ItemResult();
        $itemResult2->setType('abc')
                    ->setName('ghi')
                    ->setId($this->createMock(UuidInterface::class));
        $itemResult3 = new ItemResult();
        $itemResult3->setType('jkl')
                    ->setName('mno');

        $query = new Query();
        $query->setCombinationId($combinationId);

        $expectedNamesByTypes = new NamesByTypes();
        $expectedNamesByTypes->addName('abc', 'def')
                             ->addName('jkl', 'mno');

        $item1 = $this->createMock(Item::class);
        $item2 = $this->createMock(Item::class);
        $newItemResult1 = $this->createMock(ItemResult::class);
        $newItemResult2 = $this->createMock(ItemResult::class);

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->any())
                      ->method('getItems')
                      ->willReturn([$itemResult1, $itemResult2, $itemResult3]);
        $searchResults->expects($this->exactly(2))
                      ->method('addItem')
                      ->withConsecutive(
                          [$this->identicalTo($newItemResult1)],
                          [$this->identicalTo($newItemResult2)],
                      );

        $this->itemRepository->expects($this->once())
                             ->method('findByTypesAndNames')
                             ->with($this->identicalTo($combinationId), $this->equalTo($expectedNamesByTypes))
                             ->willReturn([$item1, $item2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($item1), $this->isInstanceOf(ItemResult::class)],
                                [$this->identicalTo($item2), $this->isInstanceOf(ItemResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $newItemResult1,
                                $newItemResult2,
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);
    }
}
