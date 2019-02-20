<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToItemResultMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the TranslationPriorityDataToItemResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToItemResultMapper
 */
class TranslationPriorityDataToItemResultMapperTest extends TestCase
{
    /**
     * Tests the getSupportedSourceClass method.
     * @covers ::getSupportedSourceClass
     */
    public function testGetSupportedSourceClass(): void
    {
        $mapper = new TranslationPriorityDataToItemResultMapper();
        $this->assertSame(TranslationPriorityData::class, $mapper->getSupportedSourceClass());
    }

    /**
     * Tests the getSupportedDestinationClass method.
     * @covers ::getSupportedDestinationClass
     */
    public function testGetSupportedDestinationClass(): void
    {
        $mapper = new TranslationPriorityDataToItemResultMapper();
        $this->assertSame(ItemResult::class, $mapper->getSupportedDestinationClass());
    }

    /**
     * Tests the map method.
     * @throws ReflectionException
     * @covers ::map
     */
    public function testMap(): void
    {
        /* @var TranslationPriorityData&MockObject $source */
        $source = $this->createMock(TranslationPriorityData::class);
        $source->expects($this->once())
               ->method('getType')
               ->willReturn('abc');
        $source->expects($this->once())
               ->method('getName')
               ->willReturn('def');
        $source->expects($this->once())
               ->method('getPriority')
               ->willReturn(42);

        /* @var ItemResult&MockObject $destination */
        $destination = $this->createMock(ItemResult::class);
        $destination->expects($this->once())
                    ->method('setType')
                    ->with($this->identicalTo('abc'))
                    ->willReturnSelf();
        $destination->expects($this->once())
                    ->method('setName')
                    ->with($this->identicalTo('def'))
                    ->willReturnSelf();
        $destination->expects($this->once())
                    ->method('setPriority')
                    ->with($this->identicalTo(42))
                    ->willReturnSelf();

        $mapper = new TranslationPriorityDataToItemResultMapper();
        $mapper->map($source, $destination);
    }
}
