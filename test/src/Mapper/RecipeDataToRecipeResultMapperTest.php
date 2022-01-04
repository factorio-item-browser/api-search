<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Mapper\RecipeDataToRecipeResultMapper;
use FactorioItemBrowser\Common\Constant\RecipeMode;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the RecipeDataToRecipeResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Mapper\RecipeDataToRecipeResultMapper
 */
class RecipeDataToRecipeResultMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new RecipeDataToRecipeResultMapper();

        $this->assertSame(RecipeData::class, $instance->getSupportedSourceClass());
        $this->assertSame(RecipeResult::class, $instance->getSupportedDestinationClass());
    }

    public function testMapWithNormalRecipe(): void
    {
        $id = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');

        $source = new RecipeData();
        $source->setId($id)
               ->setName('abc')
               ->setMode(RecipeMode::NORMAL);

        $expectedDestination = new RecipeResult();
        $expectedDestination->setNormalRecipeId($id)
                            ->setName('abc');

        $destination = new RecipeResult();

        $instance = new RecipeDataToRecipeResultMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }

    public function testMapWithExpensiveRecipe(): void
    {
        $id = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');

        $source = new RecipeData();
        $source->setId($id)
               ->setName('abc')
               ->setMode(RecipeMode::EXPENSIVE);

        $expectedDestination = new RecipeResult();
        $expectedDestination->setExpensiveRecipeId($id)
                            ->setName('abc');

        $destination = new RecipeResult();

        $instance = new RecipeDataToRecipeResultMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
