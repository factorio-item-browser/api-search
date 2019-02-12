<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult
 */
class RecipeResultTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::getType
     */
    public function testConstruct(): void
    {
        $result = new RecipeResult();

        $this->assertSame(EntityType::RECIPE, $result->getType());
        $this->assertSame('', $result->getName());
        $this->assertSame(0, $result->getNormalRecipeId());
        $this->assertSame(0, $result->getExpensiveRecipeId());
        $this->assertSame(SearchResultPriority::ANY_MATCH, $result->getPriority());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $result = new RecipeResult();

        $this->assertSame($result, $result->setName($name));
        $this->assertSame($name, $result->getName());
    }

    /**
     * Tests the setting and getting the normalRecipeId.
     * @covers ::getNormalRecipeId
     * @covers ::setNormalRecipeId
     */
    public function testSetAndGetNormalRecipeId(): void
    {
        $normalRecipeId = 42;
        $result = new RecipeResult();

        $this->assertSame($result, $result->setNormalRecipeId($normalRecipeId));
        $this->assertSame($normalRecipeId, $result->getNormalRecipeId());
    }

    /**
     * Tests the setting and getting the expensive recipe id.
     * @covers ::getExpensiveRecipeId
     * @covers ::setExpensiveRecipeId
     */
    public function testSetAndGetExpensiveRecipeId(): void
    {
        $expensiveRecipeId = 42;
        $result = new RecipeResult();

        $this->assertSame($result, $result->setExpensiveRecipeId($expensiveRecipeId));
        $this->assertSame($expensiveRecipeId, $result->getExpensiveRecipeId());
    }

    /**
     * Tests the setting and getting the priority.
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $result = new RecipeResult();

        $this->assertSame($result, $result->setPriority($priority));
        $this->assertSame($priority, $result->getPriority());
    }

    /**
     * Tests the merge method with actual data.
     * @covers ::merge
     */
    public function testMergeWithData(): void
    {
        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId(42)
                      ->setExpensiveRecipeId(1337)
                      ->setPriority(21);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId(42)
                       ->setExpensiveRecipeId(1337)
                       ->setPriority(21);

        $recipe = new RecipeResult();
        $recipe->setNormalRecipeId(24)
               ->setExpensiveRecipeId(7331)
               ->setPriority(100);

        $recipe->merge($recipeToMerge);

        $this->assertEquals($expectedRecipe, $recipe);
    }

    /**
     * Tests the merge method without actual data.
     * @covers ::merge
     */
    public function testMergeWithoutData(): void
    {
        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId(0)
                      ->setExpensiveRecipeId(0)
                      ->setPriority(100);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId(42)
                       ->setExpensiveRecipeId(1337)
                       ->setPriority(12);

        $recipe = new RecipeResult();
        $recipe->setNormalRecipeId(42)
               ->setExpensiveRecipeId(1337)
               ->setPriority(12);

        $recipe->merge($recipeToMerge);

        $this->assertEquals($expectedRecipe, $recipe);
    }
}
