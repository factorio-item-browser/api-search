<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToRecipeResultMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the TranslationPriorityDataToRecipeResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToRecipeResultMapper
 */
class TranslationPriorityDataToRecipeResultMapperTest extends TestCase
{
    /**
     * Tests the getSupportedSourceClass method.
     * @covers ::getSupportedSourceClass
     */
    public function testGetSupportedSourceClass(): void
    {
        $mapper = new TranslationPriorityDataToRecipeResultMapper();
        $this->assertSame(TranslationPriorityData::class, $mapper->getSupportedSourceClass());
    }

    /**
     * Tests the getSupportedDestinationClass method.
     * @covers ::getSupportedDestinationClass
     */
    public function testGetSupportedDestinationClass(): void
    {
        $mapper = new TranslationPriorityDataToRecipeResultMapper();
        $this->assertSame(RecipeResult::class, $mapper->getSupportedDestinationClass());
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
               ->method('getName')
               ->willReturn('abc');
        $source->expects($this->once())
               ->method('getPriority')
               ->willReturn(42);

        /* @var RecipeResult&MockObject $destination */
        $destination = $this->createMock(RecipeResult::class);
        $destination->expects($this->once())
                    ->method('setName')
                    ->with($this->identicalTo('abc'))
                    ->willReturnSelf();
        $destination->expects($this->once())
                    ->method('setPriority')
                    ->with($this->identicalTo(42))
                    ->willReturnSelf();

        $mapper = new TranslationPriorityDataToRecipeResultMapper();
        $mapper->map($source, $destination);
    }
}
