<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\TestHelper\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the MissingRecipeIdFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher
 */
class MissingRecipeIdFetcherTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked mapper manager.
     * @var MapperManagerInterface&MockObject
     */
    protected $mapperManager;

    /**
     * The mocked recipe repository.
     * @var RecipeRepository&MockObject
     */
    protected $recipeRepository;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $fetcher = new MissingRecipeIdFetcher($this->mapperManager, $this->recipeRepository);

        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
        $this->assertSame($this->recipeRepository, $this->extractProperty($fetcher, 'recipeRepository'));
    }


    /**
     * Tests the fetch method.
     * @throws MapperException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        $recipeNames = ['abc', 'def'];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);
        /* @var RecipeResult&MockObject $recipeResult1 */
        $recipeResult1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipeResult2 */
        $recipeResult2 = $this->createMock(RecipeResult::class);

        $recipes = [$recipe1, $recipe2];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($recipeResult1)],
                          [$this->identicalTo($recipeResult2)]
                      );

        /* @var MissingRecipeIdFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(MissingRecipeIdFetcher::class)
                        ->onlyMethods(['getRecipeNamesWithMissingIds', 'fetchRecipes', 'mapRecipeData'])
                        ->setConstructorArgs([$this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('getRecipeNamesWithMissingIds')
                ->with($this->identicalTo($searchResults))
                ->willReturn($recipeNames);
        $fetcher->expects($this->once())
                ->method('fetchRecipes')
                ->with($this->identicalTo($recipeNames), $this->identicalTo($query))
                ->willReturn($recipes);
        $fetcher->expects($this->exactly(2))
                ->method('mapRecipeData')
                ->withConsecutive(
                    [$this->identicalTo($recipe1)],
                    [$this->identicalTo($recipe2)]
                )
                ->willReturnOnConsecutiveCalls(
                    $recipeResult1,
                    $recipeResult2
                );

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the getRecipeNamesWithMissingIds method.
     * @throws ReflectionException
     * @covers ::getRecipeNamesWithMissingIds
     */
    public function testGetRecipeNamesWithMissingIds(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe1->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn(null);
        $recipe1->expects($this->once())
                ->method('getExpensiveRecipeId')
                ->willReturn(null);
        $recipe1->expects($this->once())
                ->method('getName')
                ->willReturn('abc');

        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);
        $recipe2->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn(null);
        $recipe2->expects($this->once())
                ->method('getExpensiveRecipeId')
                ->willReturn($id1);

        /* @var RecipeResult&MockObject $recipe3 */
        $recipe3 = $this->createMock(RecipeResult::class);
        $recipe3->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn(null);
        $recipe2->expects($this->once())
                ->method('getExpensiveRecipeId')
                ->willReturn(null);
        $recipe3->expects($this->once())
                ->method('getName')
                ->willReturn('def');

        /* @var RecipeResult&MockObject $recipe4 */
        $recipe4 = $this->createMock(RecipeResult::class);
        $recipe4->expects($this->once())
                ->method('getNormalRecipeId')
                ->willReturn($id2);

        $recipes = [$recipe1, $recipe2, $recipe3, $recipe4];
        $expectedResult = ['abc', 'def'];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getRecipes')
                      ->willReturn($recipes);

        $fetcher = new MissingRecipeIdFetcher($this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'getRecipeNamesWithMissingIds', $searchResults);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the fetchRecipes method.
     * @throws ReflectionException
     * @covers ::fetchRecipes
     */
    public function testFetchRecipes(): void
    {
        $names = ['abc', 'def'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $recipes = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method($this->identicalTo('getCombinationId'))
              ->willReturn($combinationId);

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByNames')
                               ->with($this->identicalTo($combinationId), $this->identicalTo($names))
                               ->willReturn($recipes);

        $fetcher = new MissingRecipeIdFetcher($this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'fetchRecipes', $names, $query);

        $this->assertSame($recipes, $result);
    }

    /**
     * Tests the mapRecipeData method.
     * @throws ReflectionException
     * @covers ::mapRecipeData
     */
    public function testMapRecipeData(): void
    {
        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($recipe), $this->isInstanceOf(RecipeResult::class));

        $fetcher = new MissingRecipeIdFetcher($this->mapperManager, $this->recipeRepository);
        $this->invokeMethod($fetcher, 'mapRecipeData', $recipe);
    }
}
