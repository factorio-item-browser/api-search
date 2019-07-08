<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Collection\ItemCollection;
use FactorioItemBrowser\Api\Search\Collection\RecipeCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AggregatingResultCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection
 */
class AggregatingResultCollectionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $collection = new AggregatingResultCollection();

        $this->assertInstanceOf(ItemCollection::class, $this->extractProperty($collection, 'items'));
        $this->assertInstanceOf(RecipeCollection::class, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the addItem method.
     * @throws ReflectionException
     * @covers ::addItem
     */
    public function testAddItem(): void
    {
        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);

        /* @var ItemCollection&MockObject $itemCollection */
        $itemCollection = $this->createMock(ItemCollection::class);
        $itemCollection->expects($this->once())
                       ->method('add')
                       ->with($this->identicalTo($item));

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'items', $itemCollection);

        $result = $collection->addItem($item);

        $this->assertSame($collection, $result);
    }

    /**
     * Tests the removeItem method.
     * @throws ReflectionException
     * @covers ::removeItem
     */
    public function testRemoveItem(): void
    {
        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);

        /* @var ItemCollection&MockObject $itemCollection */
        $itemCollection = $this->createMock(ItemCollection::class);
        $itemCollection->expects($this->once())
                       ->method('remove')
                       ->with($this->identicalTo($item));

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'items', $itemCollection);

        $result = $collection->removeItem($item);

        $this->assertSame($collection, $result);
    }

    /**
     * Tests the getItems method.
     * @throws ReflectionException
     * @covers ::getItems
     */
    public function testGetItems(): void
    {
        $items = [
            $this->createMock(ItemResult::class),
            $this->createMock(ItemResult::class),
        ];

        /* @var ItemCollection&MockObject $itemCollection */
        $itemCollection = $this->createMock(ItemCollection::class);
        $itemCollection->expects($this->once())
                       ->method('getAll')
                       ->willReturn($items);

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'items', $itemCollection);

        $result = $collection->getItems();

        $this->assertSame($items, $result);
    }
    
    /**
     * Tests the addRecipe method.
     * @throws ReflectionException
     * @covers ::addRecipe
     */
    public function testAddRecipe(): void
    {
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->once())
                         ->method('add')
                         ->with($this->identicalTo($recipe));

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'recipes', $recipeCollection);

        $result = $collection->addRecipe($recipe);

        $this->assertSame($collection, $result);
    }

    /**
     * Tests the removeRecipe method.
     * @throws ReflectionException
     * @covers ::removeRecipe
     */
    public function testRemoveRecipe(): void
    {
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->once())
                         ->method('remove')
                         ->with($this->identicalTo($recipe));

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'recipes', $recipeCollection);

        $result = $collection->removeRecipe($recipe);

        $this->assertSame($collection, $result);
    }

    /**
     * Tests the getRecipes method.
     * @throws ReflectionException
     * @covers ::getRecipes
     */
    public function testGetRecipes(): void
    {
        $recipes = [
            $this->createMock(RecipeResult::class),
            $this->createMock(RecipeResult::class),
        ];

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->once())
                         ->method('getAll')
                         ->willReturn($recipes);

        $collection = new AggregatingResultCollection();
        $this->injectProperty($collection, 'recipes', $recipeCollection);

        $result = $collection->getRecipes();

        $this->assertSame($recipes, $result);
    }
    
    /**
     * Tests the getMergedResults method.
     * @throws ReflectionException
     * @covers ::getMergedResults
     */
    public function testGetMergedResults(): void
    {
        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var ItemCollection&MockObject $itemCollection */
        $itemCollection = $this->createMock(ItemCollection::class);
        $itemCollection->expects($this->once())
                       ->method('getAll')
                       ->willReturn([$item]);

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->once())
                         ->method('getAll')
                         ->willReturn([$recipe]);

        /* @var AggregatingResultCollection&MockObject $collection */
        $collection = $this->getMockBuilder(AggregatingResultCollection::class)
                           ->setMethods(['compareResults'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $collection->expects($this->once())
                   ->method('compareResults')
                   ->with($this->identicalTo($item), $this->identicalTo($recipe))
                   ->willReturn(1);
        $this->injectProperty($collection, 'items', $itemCollection);
        $this->injectProperty($collection, 'recipes', $recipeCollection);

        $result = $collection->getMergedResults();

        $this->assertEquals([$recipe, $item], $result);
    }
    

    /**
     * Provides the data for the compareResults test.
     * @return array
     */
    public function provideCompareResults(): array
    {
        return [
            [
                [21, 'abc', 'def'],
                [42, 'abc', 'def'],
                -1,
            ],
            [
                [42, 'abc', 'def'],
                [21, 'abc', 'def'],
                1,
            ],
            [
                [42, 'abc', 'def'],
                [42, 'def', 'abc'],
                -1,
            ],
            [
                [42, 'def', 'abc'],
                [42, 'abc', 'def'],
                1,
            ],
            [
                [42, 'abc', 'def'],
                [42, 'abc', 'def'],
                0,
            ],
        ];
    }

    /**
     * Tests the compareResults method.
     * @param array $leftCriteria
     * @param array $rightCriteria
     * @param int $expectedResult
     * @throws ReflectionException
     * @covers ::compareResults
     * @dataProvider provideCompareResults
     */
    public function testCompareResults(array $leftCriteria, array $rightCriteria, int $expectedResult): void
    {
        /* @var ResultInterface&MockObject $leftResult */
        $leftResult = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $rightResult */
        $rightResult = $this->createMock(ResultInterface::class);

        /* @var AggregatingResultCollection&MockObject $collection */
        $collection = $this->getMockBuilder(AggregatingResultCollection::class)
                           ->setMethods(['getSortCriteria'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $collection->expects($this->exactly(2))
                   ->method('getSortCriteria')
                   ->withConsecutive(
                       [$this->identicalTo($leftResult)],
                       [$this->identicalTo($rightResult)]
                   )
                   ->willReturnOnConsecutiveCalls(
                       $leftCriteria,
                       $rightCriteria
                   );

        $result = $this->invokeMethod($collection, 'compareResults', $leftResult, $rightResult);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getSortCriteria method.
     * @throws ReflectionException
     * @covers ::getSortCriteria
     */
    public function testGetSortCriteria(): void
    {
        $priority = 42;
        $name = 'abc';
        $type = 'def';
        $expectedResult = [42, 'abc', 'def'];

        /* @var ResultInterface&MockObject $entity */
        $entity = $this->createMock(ResultInterface::class);
        $entity->expects($this->once())
               ->method('getPriority')
               ->willReturn($priority);
        $entity->expects($this->once())
               ->method('getName')
               ->willReturn($name);
        $entity->expects($this->once())
               ->method('getType')
               ->willReturn($type);

        $collection = new AggregatingResultCollection();
        $result = $this->invokeMethod($collection, 'getSortCriteria', $entity);

        $this->assertEquals($expectedResult, $result);
    }
}
