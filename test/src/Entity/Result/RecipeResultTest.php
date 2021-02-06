<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the RecipeResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult
 */
class RecipeResultTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new RecipeResult();

        $this->assertSame(EntityType::RECIPE, $instance->getType());
        $this->assertSame('', $instance->getName());
        $this->assertNull($instance->getNormalRecipeId());
        $this->assertNull($instance->getExpensiveRecipeId());
        $this->assertSame(SearchResultPriority::ANY_MATCH, $instance->getPriority());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = new RecipeResult();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testSetAndGetNormalRecipeId(): void
    {
        $normalRecipeId = $this->createMock(UuidInterface::class);
        $instance = new RecipeResult();

        $this->assertSame($instance, $instance->setNormalRecipeId($normalRecipeId));
        $this->assertSame($normalRecipeId, $instance->getNormalRecipeId());
    }

    public function testSetAndGetExpensiveRecipeId(): void
    {
        $expensiveRecipeId = $this->createMock(UuidInterface::class);
        $instance = new RecipeResult();

        $this->assertSame($instance, $instance->setExpensiveRecipeId($expensiveRecipeId));
        $this->assertSame($expensiveRecipeId, $instance->getExpensiveRecipeId());
    }

    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $instance = new RecipeResult();

        $this->assertSame($instance, $instance->setPriority($priority));
        $this->assertSame($priority, $instance->getPriority());
    }

    public function testMergeWithData(): void
    {
        $id1 = $this->createMock(UuidInterface::class);
        $id2 = $this->createMock(UuidInterface::class);
        $id3 = $this->createMock(UuidInterface::class);
        $id4 = $this->createMock(UuidInterface::class);

        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId($id1)
                      ->setExpensiveRecipeId($id2)
                      ->setPriority(21);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId($id1)
                       ->setExpensiveRecipeId($id2)
                       ->setPriority(21);

        $instance = new RecipeResult();
        $instance->setNormalRecipeId($id3)
                 ->setExpensiveRecipeId($id4)
                 ->setPriority(100);

        $instance->merge($recipeToMerge);

        $this->assertEquals($expectedRecipe, $instance);
    }

    public function testMergeWithoutData(): void
    {
        $id1 = $this->createMock(UuidInterface::class);
        $id2 = $this->createMock(UuidInterface::class);

        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId(null)
                      ->setExpensiveRecipeId(null)
                      ->setPriority(100);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId($id1)
                       ->setExpensiveRecipeId($id2)
                       ->setPriority(12);

        $instance = new RecipeResult();
        $instance->setNormalRecipeId($id1)
                 ->setExpensiveRecipeId($id2)
                 ->setPriority(12);

        $instance->merge($recipeToMerge);

        $this->assertEquals($expectedRecipe, $instance);
    }
}
