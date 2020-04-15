<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Mapper\ItemToItemResultMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ItemToItemResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Mapper\ItemToItemResultMapper
 */
class ItemToItemResultMapperTest extends TestCase
{
    /**
     * Tests the getSupportedSourceClass method.
     * @covers ::getSupportedSourceClass
     */
    public function testGetSupportedSourceClass(): void
    {
        $mapper = new ItemToItemResultMapper();
        $this->assertSame(Item::class, $mapper->getSupportedSourceClass());
    }

    /**
     * Tests the getSupportedDestinationClass method.
     * @covers ::getSupportedDestinationClass
     */
    public function testGetSupportedDestinationClass(): void
    {
        $mapper = new ItemToItemResultMapper();
        $this->assertSame(ItemResult::class, $mapper->getSupportedDestinationClass());
    }

    /**
     * Tests the map method.
     * @covers ::map
     */
    public function testMap(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);

        /* @var Item&MockObject $source */
        $source = $this->createMock(Item::class);
        $source->expects($this->once())
               ->method('getId')
               ->willReturn($id);
        $source->expects($this->once())
               ->method('getType')
               ->willReturn('abc');
        $source->expects($this->once())
               ->method('getName')
               ->willReturn('def');

        /* @var ItemResult&MockObject $destination */
        $destination = $this->createMock(ItemResult::class);
        $destination->expects($this->once())
                    ->method('setId')
                    ->with($this->identicalTo($id))
                    ->willReturnSelf();
        $destination->expects($this->once())
                    ->method('setType')
                    ->with($this->identicalTo('abc'))
                    ->willReturnSelf();
        $destination->expects($this->once())
                    ->method('setName')
                    ->with($this->identicalTo('def'))
                    ->willReturnSelf();

        $mapper = new ItemToItemResultMapper();
        $mapper->map($source, $destination);
    }
}
