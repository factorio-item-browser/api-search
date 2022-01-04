<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
        $normalRecipeId = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $instance = new RecipeResult();

        $this->assertSame($instance, $instance->setNormalRecipeId($normalRecipeId));
        $this->assertSame($normalRecipeId, $instance->getNormalRecipeId());
    }

    public function testSetAndGetExpensiveRecipeId(): void
    {
        $expensiveRecipeId = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
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
        $id1 = Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1');
        $id2 = Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e');
        $id3 = Uuid::fromString('37451ec1-6b60-4870-a24f-41afd4cdd477');
        $id4 = Uuid::fromString('4a3a8c6c-8d68-41f6-997a-8761c380cd7d');

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
        $id1 = Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1');
        $id2 = Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e');

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
