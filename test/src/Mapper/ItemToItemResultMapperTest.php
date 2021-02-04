<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Mapper\ItemToItemResultMapper;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ItemToItemResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Mapper\ItemToItemResultMapper
 */
class ItemToItemResultMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new ItemToItemResultMapper();

        $this->assertSame(Item::class, $instance->getSupportedSourceClass());
        $this->assertSame(ItemResult::class, $instance->getSupportedDestinationClass());
    }

    public function testMap(): void
    {
        $id = $this->createMock(UuidInterface::class);

        $source = new Item();
        $source->setId($id)
               ->setType('abc')
               ->setName('def');

        $expectedDestination = new ItemResult();
        $expectedDestination->setId($id)
                            ->setType('abc')
                            ->setName('def');

        $destination = new ItemResult();

        $instance = new ItemToItemResultMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
