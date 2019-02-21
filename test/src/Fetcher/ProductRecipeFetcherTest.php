<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\Common\Test\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Filter\DataFilter;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\ProductRecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ProductRecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\ProductRecipeFetcher
 */
class ProductRecipeFetcherTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked data filter.
     * @var DataFilter&MockObject
     */
    protected $dataFilter;

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
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dataFilter = $this->createMock(DataFilter::class);
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
        $fetcher = new ProductRecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);

        $this->assertSame($this->dataFilter, $this->extractProperty($fetcher, 'dataFilter'));
        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
        $this->assertSame($this->recipeRepository, $this->extractProperty($fetcher, 'recipeRepository'));
    }

    /**
     * Tests the fetch method.
     * @throws ReflectionException
     * @throws MapperException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        $items = [
            42 => $this->createMock(ItemResult::class),
            1337 => $this->createMock(ItemResult::class),
        ];
        $expectedItemIds = [42, 1337];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe3 */
        $recipe3 = $this->createMock(RecipeData::class);

        $recipes = [$recipe1, $recipe2, $recipe3];
        $filteredRecipes = [$recipe1, $recipe2];

        $this->dataFilter->expects($this->once())
                         ->method('filter')
                         ->with($this->identicalTo($recipes))
                         ->willReturn($filteredRecipes);

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->setMethods(['getItems', 'fetchProductRecipes', 'matchRecipeToItems'])
                        ->setConstructorArgs([$this->dataFilter, $this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('getItems')
                ->with($this->identicalTo($searchResults))
                ->willReturn($items);
        $fetcher->expects($this->once())
                ->method('fetchProductRecipes')
                ->with($this->equalTo($expectedItemIds), $this->identicalTo($query))
                ->willReturn($recipes);
        $fetcher->expects($this->exactly(2))
                ->method('matchRecipeToItems')
                ->withConsecutive(
                    [$this->identicalTo($recipe1), $this->identicalTo($items)],
                    [$this->identicalTo($recipe2), $this->identicalTo($items)]
                );

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the getItems method.
     * @throws ReflectionException
     * @covers ::getItems
     */
    public function testGetItems(): void
    {
        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn(42);

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn(1337);

        $items = [$item1, $item2];
        $expectedResult = [
            42 => $item1,
            1337 => $item2,
        ];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getItems')
                      ->willReturn($items);

        $fetcher = new ProductRecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'getItems', $searchResults);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the fetchProductRecipes method.
     * @throws ReflectionException
     * @covers ::fetchProductRecipes
     */
    public function testFetchProductRecipes(): void
    {
        $itemIds = [42, 1337];
        $modCombinationIds = [21, 7331];

        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);

        $recipes = [$recipe1, $recipe2];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getModCombinationIds')
              ->willReturn($modCombinationIds);

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByProductItemIds')
                               ->with($this->identicalTo($itemIds), $this->identicalTo($modCombinationIds))
                               ->willReturn($recipes);

        $fetcher = new ProductRecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'fetchProductRecipes', $itemIds, $query);

        $this->assertSame($recipes, $result);
    }

    /**
     * Tests the matchRecipeToItems method with an actual match.
     * @throws ReflectionException
     * @covers ::matchRecipeToItems
     */
    public function testMatchRecipeToItemsWithMatch(): void
    {
        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getItemId')
               ->willReturn(42);

        /* @var RecipeResult&MockObject $mappedRecipe */
        $mappedRecipe = $this->createMock(RecipeResult::class);

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->once())
              ->method('addRecipe')
              ->with($this->identicalTo($mappedRecipe));

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->never())
              ->method('addRecipe');

        $items = [
            42 => $item1,
            1337 => $item2,
        ];

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->setMethods(['mapRecipe'])
                        ->setConstructorArgs([$this->dataFilter, $this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('mapRecipe')
                ->with($this->identicalTo($recipe))
                ->willReturn($mappedRecipe);

        $this->invokeMethod($fetcher, 'matchRecipeToItems', $recipe, $items);
    }

    /**
     * Tests the matchRecipeToItems method without an actual match.
     * @throws ReflectionException
     * @covers ::matchRecipeToItems
     */
    public function testMatchRecipeToItemsWithoutMatch(): void
    {
        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getItemId')
               ->willReturn(21);

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->never())
              ->method('addRecipe');

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->never())
              ->method('addRecipe');

        $items = [
            42 => $item1,
            1337 => $item2,
        ];

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->setMethods(['mapRecipe'])
                        ->setConstructorArgs([$this->dataFilter, $this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->never())
                ->method('mapRecipe');

        $this->invokeMethod($fetcher, 'matchRecipeToItems', $recipe, $items);
    }

    /**
     * Tests the mapRecipe method.
     * @throws ReflectionException
     * @covers ::mapRecipe
     */
    public function testMapRecipe(): void
    {
        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($recipe), $this->isInstanceOf(RecipeResult::class));

        $fetcher = new ProductRecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        $this->invokeMethod($fetcher, 'mapRecipe', $recipe);
    }
}
