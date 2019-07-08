<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\ItemCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ItemCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Collection\ItemCollection
 */
class ItemCollectionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $collection = new ItemCollection();

        $this->assertSame([], $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the add method without having an actual key.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithoutKey(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $newItem */
        $newItem = $this->createMock(ItemResult::class);

        $items = [
            'abc' => $item1,
            $item2,
        ];
        $expectedItems = [
            'abc' => $item1,
            $item2,
            $newItem,
        ];

        /* @var ItemCollection&MockObject $collection */
        $collection = $this->getMockBuilder(ItemCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newItem))
                   ->willReturn('');
        $this->injectProperty($collection, 'items', $items);

        $result = $collection->add($newItem);
        $this->assertSame($collection, $result);
        $this->assertEquals($expectedItems, $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the add method with a match.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithMatch(): void
    {
        /* @var ItemResult&MockObject $newItem */
        $newItem = $this->createMock(ItemResult::class);

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->once())
                ->method('merge')
                ->with($this->identicalTo($newItem));

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);

        $key = 'abc';
        $items = [
            'abc' => $item1,
            'def' => $item2,
        ];

        /* @var ItemCollection&MockObject $collection */
        $collection = $this->getMockBuilder(ItemCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newItem))
                   ->willReturn($key);
        $this->injectProperty($collection, 'items', $items);

        $result = $collection->add($newItem);
        $this->assertSame($collection, $result);
        $this->assertEquals($items, $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the add method without a match.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithoutMatch(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $newItem */
        $newItem = $this->createMock(ItemResult::class);

        $key = 'ghi';
        $items = [
            'abc' => $item1,
            'def' => $item2,
        ];
        $expectedItems = [
            'abc' => $item1,
            'def' => $item2,
            'ghi' => $newItem,
        ];

        /* @var ItemCollection&MockObject $collection */
        $collection = $this->getMockBuilder(ItemCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newItem))
                   ->willReturn($key);
        $this->injectProperty($collection, 'items', $items);

        $result = $collection->add($newItem);
        $this->assertSame($collection, $result);
        $this->assertEquals($expectedItems, $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the remove method with a hit.
     * @throws ReflectionException
     * @covers ::remove
     */
    public function testRemoveWithHit(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);

        $key = 'abc';
        $items = [
            'abc' => $item1,
            'def' => $item2,
        ];
        $expectedItems = [
            'def' => $item2,
        ];

        /* @var ItemCollection&MockObject $collection */
        $collection = $this->getMockBuilder(ItemCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($item1))
                   ->willReturn($key);
        $this->injectProperty($collection, 'items', $items);

        $result = $collection->remove($item1);

        $this->assertSame($collection, $result);
        $this->assertEquals($expectedItems, $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the remove method without a hit.
     * @throws ReflectionException
     * @covers ::remove
     */
    public function testRemoveWithoutHit(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);

        $key = 'ghi';
        $items = [
            'abc' => $item1,
        ];

        /* @var ItemCollection&MockObject $collection */
        $collection = $this->getMockBuilder(ItemCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($item2))
                   ->willReturn($key);
        $this->injectProperty($collection, 'items', $items);

        $result = $collection->remove($item2);

        $this->assertSame($collection, $result);
        $this->assertEquals($items, $this->extractProperty($collection, 'items'));
    }

    /**
     * Tests the getAll method.
     * @throws ReflectionException
     * @covers ::getAll
     */
    public function testGetAll(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);

        $items = [
            'abc' => $item1,
            'def' => $item2,
        ];
        $expectedResult = [$item1, $item2];

        $collection = new ItemCollection();
        $this->injectProperty($collection, 'items', $items);
        $result = $collection->getAll();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Provides the data for the getKey test.
     * @return array
     */
    public function provideGetKey(): array
    {
        return [
            ['abc', 'def', 'abc|def'],
            ['', '', ''],
        ];
    }

    /**
     * Tests the getKey method.
     * @param string $type
     * @param string $name
     * @param string $expectedResult
     * @throws ReflectionException
     * @covers ::getKey
     * @dataProvider provideGetKey
     */
    public function testGetKey(string $type, string $name, string $expectedResult): void
    {
        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        $item->expects($this->once())
             ->method('getType')
             ->willReturn($type);
        $item->expects($this->once())
             ->method('getName')
             ->willReturn($name);

        $collection = new ItemCollection();
        $result = $this->invokeMethod($collection, 'getKey', $item);

        $this->assertSame($expectedResult, $result);
    }
}
