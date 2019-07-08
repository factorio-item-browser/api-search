<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\DuplicateRecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the DuplicateRecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\DuplicateRecipeFetcher
 */
class DuplicateRecipeFetcherTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the fetch method.
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        $items1 = [
            $this->createMock(ItemResult::class),
            $this->createMock(ItemResult::class),
        ];
        $items2 = [
            $this->createMock(ItemResult::class),
        ];
        $itemsByRecipeIds = [
            42 => $items1,
            1337 => $items2,
        ];

        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe1->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn(42);
        $recipe1->expects($this->once())
                ->method('getExpensiveRecipeId')
                ->willReturn(21);

        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);
        $recipe2->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn(7331);
        $recipe2->expects($this->once())
                ->method('getExpensiveRecipeId')
                ->willReturn(1337);

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getRecipes')
                      ->willReturn([$recipe1, $recipe2]);

        /* @var DuplicateRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(DuplicateRecipeFetcher::class)
                        ->setMethods(['getItemsByRecipeIds', 'filterRecipe'])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('getItemsByRecipeIds')
                ->with($this->identicalTo($searchResults))
                ->willReturn($itemsByRecipeIds);
        $fetcher->expects($this->exactly(4))
                ->method('filterRecipe')
                ->withConsecutive(
                    [$this->identicalTo($recipe1), $this->identicalTo($items1), $this->identicalTo($searchResults)],
                    [$this->identicalTo($recipe1), $this->identicalTo([]), $this->identicalTo($searchResults)],
                    [$this->identicalTo($recipe2), $this->identicalTo([]), $this->identicalTo($searchResults)],
                    [$this->identicalTo($recipe2), $this->identicalTo($items2), $this->identicalTo($searchResults)]
                );

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the getItemsByRecipeIds method.
     * @throws ReflectionException
     * @covers ::getItemsByRecipeIds
     */
    public function testGetItemsByRecipeIds(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe1->expects($this->atLeastOnce())
                ->method('getNormalRecipeId')
                ->willReturn(42);
        $recipe1->expects($this->atLeastOnce())
                ->method('getExpensiveRecipeId')
                ->willReturn(1337);

        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);
        $recipe2->expects($this->atLeastOnce())
                ->method('getNormalRecipeId')
                ->willReturn(21);
        $recipe2->expects($this->atLeastOnce())
                ->method('getExpensiveRecipeId')
                ->willReturn(0);

        /* @var RecipeResult&MockObject $recipe3 */
        $recipe3 = $this->createMock(RecipeResult::class);
        $recipe3->expects($this->atLeastOnce())
                ->method('getNormalRecipeId')
                ->willReturn(0);
        $recipe3->expects($this->atLeastOnce())
                ->method('getExpensiveRecipeId')
                ->willReturn(1337);

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->once())
              ->method('getRecipes')
              ->willReturn([$recipe1, $recipe2]);

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->once())
              ->method('getRecipes')
              ->willReturn([$recipe3]);

        $expectedResult = [
            42 => [$item1],
            1337 => [$item1, $item2],
            21 => [$item2],
        ];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getItems')
                      ->willReturn([$item1, $item2]);

        $fetcher = new DuplicateRecipeFetcher();
        $result = $this->invokeMethod($fetcher, 'getItemsByRecipeIds', $searchResults);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the filterRecipe method with actual items.
     * @throws ReflectionException
     * @covers ::filterRecipe
     */
    public function testFilterRecipeWithItems(): void
    {
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getPriority')
               ->willReturn(42);

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->once())
              ->method('getPriority')
              ->willReturn(21);
        $item1->expects($this->once())
              ->method('setPriority')
              ->with($this->identicalTo(21));

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->once())
              ->method('getPriority')
              ->willReturn(1337);
        $item2->expects($this->once())
              ->method('setPriority')
              ->with($this->identicalTo(42));

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('removeRecipe')
                      ->with($this->identicalTo($recipe));

        $fetcher = new DuplicateRecipeFetcher();
        $this->invokeMethod($fetcher, 'filterRecipe', $recipe, [$item1, $item2], $searchResults);
    }

    /**
     * Tests the filterRecipe method without actual items.
     * @throws ReflectionException
     * @covers ::filterRecipe
     */
    public function testFilterRecipeWithoutItems(): void
    {
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->never())
                      ->method('removeRecipe');

        $fetcher = new DuplicateRecipeFetcher();
        $this->invokeMethod($fetcher, 'filterRecipe', $recipe, [], $searchResults);
    }
}
