<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\DuplicateRecipeFetcher;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the DuplicateRecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\DuplicateRecipeFetcher
 */
class DuplicateRecipeFetcherTest extends TestCase
{
    public function testFetch(): void
    {
        $id1 = Uuid::fromString('1b467edb-1fea-4f97-b829-ce5f666f2095');
        $id2 = Uuid::fromString('2e061889-89f8-47a6-891a-3ab1ddd64123');
        $id3 = Uuid::fromString('32702df6-eebc-4bc2-be35-315678fa53d7');
        $id4 = Uuid::fromString('462456b5-de9a-4675-ace0-dbe4c960eb01');

        $item1Recipe1 = new RecipeResult();
        $item1Recipe1->setNormalRecipeId($id1);
        $item1Recipe2 = new RecipeResult();
        $item1Recipe2->setExpensiveRecipeId($id2);
        $item1 = new ItemResult();
        $item1->setPriority(42)
              ->addRecipe($item1Recipe1)
              ->addRecipe($item1Recipe2);

        $recipe1 = new RecipeResult();
        $recipe1->setPriority(21)
                ->setNormalRecipeId($id1);
        $recipe2 = new RecipeResult();
        $recipe2->setPriority(1)
                ->setNormalRecipeId($id3)
                ->setNormalRecipeId($id4);
        $recipe3 = new RecipeResult();
        $recipe3->setPriority(1337)
                ->setExpensiveRecipeId($id2);

        $query = $this->createMock(Query::class);

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->any())
                      ->method('getRecipes')
                      ->willReturn([$recipe1, $recipe3]);
        $searchResults->expects($this->any())
                      ->method('getItems')
                      ->willReturn([$item1]);
        $searchResults->expects($this->exactly(2))
                      ->method('removeRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($recipe1)],
                          [$this->identicalTo($recipe3)],
                      );

        $instance = new DuplicateRecipeFetcher();
        $instance->fetch($query, $searchResults);

        $this->assertSame($item1->getPriority(), 21);
    }
}
