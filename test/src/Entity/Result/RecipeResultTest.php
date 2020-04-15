<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

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
        $this->assertNull($result->getNormalRecipeId());
        $this->assertNull($result->getExpensiveRecipeId());
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
        /* @var UuidInterface&MockObject $normalRecipeId */
        $normalRecipeId = $this->createMock(UuidInterface::class);
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
        /* @var UuidInterface&MockObject $expensiveRecipeId */
        $expensiveRecipeId = $this->createMock(UuidInterface::class);
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
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id3 */
        $id3 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id4 */
        $id4 = $this->createMock(UuidInterface::class);

        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId($id1)
                      ->setExpensiveRecipeId($id2)
                      ->setPriority(21);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId($id1)
                       ->setExpensiveRecipeId($id2)
                       ->setPriority(21);

        $recipe = new RecipeResult();
        $recipe->setNormalRecipeId($id3)
               ->setExpensiveRecipeId($id4)
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
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

        $recipeToMerge = new RecipeResult();
        $recipeToMerge->setNormalRecipeId(null)
                      ->setExpensiveRecipeId(null)
                      ->setPriority(100);

        $expectedRecipe = new RecipeResult();
        $expectedRecipe->setNormalRecipeId($id1)
                       ->setExpensiveRecipeId($id2)
                       ->setPriority(12);

        $recipe = new RecipeResult();
        $recipe->setNormalRecipeId($id1)
               ->setExpensiveRecipeId($id2)
               ->setPriority(12);

        $recipe->merge($recipeToMerge);

        $this->assertEquals($expectedRecipe, $recipe);
    }
}
