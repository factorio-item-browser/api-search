<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\RecipeCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the RecipeCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Collection\RecipeCollection
 */
class RecipeCollectionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $collection = new RecipeCollection();

        $this->assertSame([], $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the add method without having an actual key.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithoutKey(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $newRecipe */
        $newRecipe = $this->createMock(RecipeResult::class);

        $recipes = [
            'abc' => $recipe1,
            $recipe2,
        ];
        $expectedRecipes = [
            'abc' => $recipe1,
            $recipe2,
            $newRecipe,
        ];

        /* @var RecipeCollection&MockObject $collection */
        $collection = $this->getMockBuilder(RecipeCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newRecipe))
                   ->willReturn('');
        $this->injectProperty($collection, 'recipes', $recipes);

        $result = $collection->add($newRecipe);
        $this->assertSame($collection, $result);
        $this->assertEquals($expectedRecipes, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the add method with a match.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithMatch(): void
    {
        /* @var RecipeResult&MockObject $newRecipe */
        $newRecipe = $this->createMock(RecipeResult::class);

        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe1->expects($this->once())
                ->method('merge')
                ->with($this->identicalTo($newRecipe));

        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $key = 'abc';
        $recipes = [
            'abc' => $recipe1,
            'def' => $recipe2,
        ];

        /* @var RecipeCollection&MockObject $collection */
        $collection = $this->getMockBuilder(RecipeCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newRecipe))
                   ->willReturn($key);
        $this->injectProperty($collection, 'recipes', $recipes);

        $result = $collection->add($newRecipe);
        $this->assertSame($collection, $result);
        $this->assertEquals($recipes, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the add method without a match.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAddWithoutMatch(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $newRecipe */
        $newRecipe = $this->createMock(RecipeResult::class);

        $key = 'ghi';
        $recipes = [
            'abc' => $recipe1,
            'def' => $recipe2,
        ];
        $expectedRecipes = [
            'abc' => $recipe1,
            'def' => $recipe2,
            'ghi' => $newRecipe,
        ];

        /* @var RecipeCollection&MockObject $collection */
        $collection = $this->getMockBuilder(RecipeCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($newRecipe))
                   ->willReturn($key);
        $this->injectProperty($collection, 'recipes', $recipes);

        $result = $collection->add($newRecipe);
        $this->assertSame($collection, $result);
        $this->assertEquals($expectedRecipes, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the remove method with a hit.
     * @throws ReflectionException
     * @covers ::remove
     */
    public function testRemoveWithHit(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $key = 'abc';
        $recipes = [
            'abc' => $recipe1,
            'def' => $recipe2,
        ];
        $expectedRecipes = [
            'def' => $recipe2,
        ];

        /* @var RecipeCollection&MockObject $collection */
        $collection = $this->getMockBuilder(RecipeCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($recipe1))
                   ->willReturn($key);
        $this->injectProperty($collection, 'recipes', $recipes);

        $result = $collection->remove($recipe1);

        $this->assertSame($collection, $result);
        $this->assertEquals($expectedRecipes, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the remove method without a hit.
     * @throws ReflectionException
     * @covers ::remove
     */
    public function testRemoveWithoutHit(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $key = 'ghi';
        $recipes = [
            'abc' => $recipe1,
        ];

        /* @var RecipeCollection&MockObject $collection */
        $collection = $this->getMockBuilder(RecipeCollection::class)
                           ->setMethods(['getKey'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getKey')
                   ->with($this->identicalTo($recipe2))
                   ->willReturn($key);
        $this->injectProperty($collection, 'recipes', $recipes);

        $result = $collection->remove($recipe2);

        $this->assertSame($collection, $result);
        $this->assertEquals($recipes, $this->extractProperty($collection, 'recipes'));
    }

    /**
     * Tests the getAll method.
     * @throws ReflectionException
     * @covers ::getAll
     */
    public function testGetAll(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $recipes = [
            'abc' => $recipe1,
            'def' => $recipe2,
        ];
        $expectedResult = [$recipe1, $recipe2];

        $collection = new RecipeCollection();
        $this->injectProperty($collection, 'recipes', $recipes);
        $result = $collection->getAll();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the getKey method.
     * @throws ReflectionException
     * @covers ::getKey
     */
    public function testGetKey(): void
    {
        $name = 'abc';

        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);
        $recipe->expects($this->once())
               ->method('getName')
               ->willReturn($name);

        $collection = new RecipeCollection();
        $result = $this->invokeMethod($collection, 'getKey', $recipe);

        $this->assertSame($name, $result);
    }
}
