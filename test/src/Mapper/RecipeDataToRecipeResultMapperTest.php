<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Mapper\RecipeDataToRecipeResultMapper;
use FactorioItemBrowser\Common\Constant\RecipeMode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the RecipeDataToRecipeResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Mapper\RecipeDataToRecipeResultMapper
 */
class RecipeDataToRecipeResultMapperTest extends TestCase
{
    /**
     * Tests the getSupportedSourceClass method.
     * @covers ::getSupportedSourceClass
     */
    public function testGetSupportedSourceClass(): void
    {
        $mapper = new RecipeDataToRecipeResultMapper();
        $this->assertSame(RecipeData::class, $mapper->getSupportedSourceClass());
    }

    /**
     * Tests the getSupportedDestinationClass method.
     * @covers ::getSupportedDestinationClass
     */
    public function testGetSupportedDestinationClass(): void
    {
        $mapper = new RecipeDataToRecipeResultMapper();
        $this->assertSame(RecipeResult::class, $mapper->getSupportedDestinationClass());
    }

    /**
     * Tests the map method with a normal recipe.
     * @covers ::map
     */
    public function testMapWithNormalRecipe(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);

        /* @var RecipeData&MockObject $source */
        $source = $this->createMock(RecipeData::class);
        $source->expects($this->once())
               ->method('getName')
               ->willReturn('abc');
        $source->expects($this->once())
               ->method('getMode')
               ->willReturn(RecipeMode::NORMAL);
        $source->expects($this->once())
               ->method('getId')
               ->willReturn($id);

        /* @var RecipeResult&MockObject $destination */
        $destination = $this->createMock(RecipeResult::class);
        $destination->expects($this->once())
                    ->method('setName')
                    ->with($this->identicalTo('abc'));
        $destination->expects($this->once())
                    ->method('setNormalRecipeId')
                    ->with($this->identicalTo($id));

        $mapper = new RecipeDataToRecipeResultMapper();
        $mapper->map($source, $destination);
    }

    /**
     * Tests the map method.
     * @covers ::map
     */
    public function testMapWithExpensiveRecipe(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);

        /* @var RecipeData&MockObject $source */
        $source = $this->createMock(RecipeData::class);
        $source->expects($this->once())
               ->method('getName')
               ->willReturn('abc');
        $source->expects($this->once())
               ->method('getMode')
               ->willReturn(RecipeMode::EXPENSIVE);
        $source->expects($this->once())
               ->method('getId')
               ->willReturn($id);

        /* @var RecipeResult&MockObject $destination */
        $destination = $this->createMock(RecipeResult::class);
        $destination->expects($this->once())
                    ->method('setName')
                    ->with($this->identicalTo('abc'));
        $destination->expects($this->once())
                    ->method('setExpensiveRecipeId')
                    ->with($this->identicalTo($id));

        $mapper = new RecipeDataToRecipeResultMapper();
        $mapper->map($source, $destination);
    }
}
