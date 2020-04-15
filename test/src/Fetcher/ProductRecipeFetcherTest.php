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
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\ProductRecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
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
        $fetcher = new ProductRecipeFetcher($this->mapperManager, $this->recipeRepository);

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
        $items = [
            $this->createMock(ItemResult::class),
            $this->createMock(ItemResult::class),
        ];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);

        $recipes = [$recipe1, $recipe2];

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->onlyMethods(['getItems', 'fetchProductRecipes', 'matchRecipeToItems'])
                        ->setConstructorArgs([$this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('getItems')
                ->with($this->identicalTo($searchResults))
                ->willReturn($items);
        $fetcher->expects($this->once())
                ->method('fetchProductRecipes')
                ->with($this->equalTo($items), $this->identicalTo($query))
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
        $id1 = Uuid::fromString('40718ef3-3d81-4c6f-ac42-650d4c38d226');
        $id2 = Uuid::fromString('79c6ee59-57b3-4fe1-a766-10c1454cdc8a');

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn($id1);

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn(null);

        /* @var ItemResult&MockObject $item3 */
        $item3 = $this->createMock(ItemResult::class);
        $item3->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn($id2);


        $items = [$item1, $item2, $item3];
        $expectedResult = [
            '40718ef3-3d81-4c6f-ac42-650d4c38d226' => $item1,
            '79c6ee59-57b3-4fe1-a766-10c1454cdc8a' => $item3,
        ];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getItems')
                      ->willReturn($items);

        $fetcher = new ProductRecipeFetcher($this->mapperManager, $this->recipeRepository);
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
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $itemId1 */
        $itemId1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $itemId2 */
        $itemId2 = $this->createMock(UuidInterface::class);
        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);

        $expectedItemIds = [$itemId1, $itemId2];
        $recipes = [$recipe1, $recipe2];

        /* @var ItemResult&MockObject $item1 */
        $item1 = $this->createMock(ItemResult::class);
        $item1->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn($itemId1);

        /* @var ItemResult&MockObject $item2 */
        $item2 = $this->createMock(ItemResult::class);
        $item2->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn(null);

        /* @var ItemResult&MockObject $item3 */
        $item3 = $this->createMock(ItemResult::class);
        $item3->expects($this->atLeastOnce())
              ->method('getId')
              ->willReturn($itemId2);

        $items = [$item1, $item2, $item3];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getCombinationId')
              ->willReturn($combinationId);

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByProductItemIds')
                               ->with($this->identicalTo($combinationId), $this->identicalTo($expectedItemIds))
                               ->willReturn($recipes);

        $fetcher = new ProductRecipeFetcher($this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'fetchProductRecipes', $items, $query);

        $this->assertSame($recipes, $result);
    }

    /**
     * Tests the matchRecipeToItems method.
     * @throws ReflectionException
     * @covers ::matchRecipeToItems
     */
    public function testMatchRecipeToItems(): void
    {
        $recipeItemId = Uuid::fromString('40718ef3-3d81-4c6f-ac42-650d4c38d226');

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
            '40718ef3-3d81-4c6f-ac42-650d4c38d226' => $item1,
            '79c6ee59-57b3-4fe1-a766-10c1454cdc8a' => $item2,
        ];

        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getItemId')
               ->willReturn($recipeItemId);

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->onlyMethods(['mapRecipe'])
                        ->setConstructorArgs([$this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('mapRecipe')
                ->with($this->identicalTo($recipe))
                ->willReturn($mappedRecipe);

        $this->invokeMethod($fetcher, 'matchRecipeToItems', $recipe, $items);
    }

    /**
     * Tests the matchRecipeToItems method.
     * @throws ReflectionException
     * @covers ::matchRecipeToItems
     */
    public function testMatchRecipeToItemsWithoutMatch(): void
    {
        $recipeItemId = Uuid::fromString('40718ef3-3d81-4c6f-ac42-650d4c38d226');

        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        $item->expects($this->never())
              ->method('addRecipe');

        $items = [
            '79c6ee59-57b3-4fe1-a766-10c1454cdc8a' => $item,
        ];

        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getItemId')
               ->willReturn($recipeItemId);

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->onlyMethods(['mapRecipe'])
                        ->setConstructorArgs([$this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->never())
                ->method('mapRecipe');

        $this->invokeMethod($fetcher, 'matchRecipeToItems', $recipe, $items);
    }

    /**
     * Tests the matchRecipeToItems method.
     * @throws ReflectionException
     * @covers ::matchRecipeToItems
     */
    public function testMatchRecipeToItemsWithoutItemId(): void
    {
        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        $item->expects($this->never())
              ->method('addRecipe');

        $items = [
            '40718ef3-3d81-4c6f-ac42-650d4c38d226' => $item,
        ];

        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);
        $recipe->expects($this->atLeastOnce())
               ->method('getItemId')
               ->willReturn(null);

        /* @var ProductRecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(ProductRecipeFetcher::class)
                        ->onlyMethods(['mapRecipe'])
                        ->setConstructorArgs([$this->mapperManager, $this->recipeRepository])
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

        $fetcher = new ProductRecipeFetcher($this->mapperManager, $this->recipeRepository);
        $this->invokeMethod($fetcher, 'mapRecipe', $recipe);
    }
}
