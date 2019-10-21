<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Collection\RecipeCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the ItemResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\Result\ItemResult
 */
class ItemResultTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $result = new ItemResult();

        $this->assertSame('', $result->getType());
        $this->assertSame('', $result->getName());
        $this->assertNull($result->getId());
        $this->assertSame(SearchResultPriority::ANY_MATCH, $result->getPriority());
        $this->assertInstanceOf(RecipeCollection::class, $this->extractProperty($result, 'recipes'));
    }

    /**
     * Tests the setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $result = new ItemResult();

        $this->assertSame($result, $result->setType($type));
        $this->assertSame($type, $result->getType());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $result = new ItemResult();

        $this->assertSame($result, $result->setName($name));
        $this->assertSame($name, $result->getName());
    }

    /**
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $result = new ItemResult();

        $this->assertSame($result, $result->setId($id));
        $this->assertSame($id, $result->getId());
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

        $itemResult = new ItemResult();
        $this->injectProperty($itemResult, 'recipes', $recipeCollection);
        $result = $itemResult->addRecipe($recipe);

        $this->assertSame($itemResult, $result);
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

        $itemResult = new ItemResult();
        $this->injectProperty($itemResult, 'recipes', $recipeCollection);

        $result = $itemResult->getRecipes();
        $this->assertSame($recipes, $result);
    }

    /**
     * Tests the setting and getting the priority.
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $result = new ItemResult();

        $this->assertSame($result, $result->setPriority($priority));
        $this->assertSame($priority, $result->getPriority());
    }

    /**
     * Tests the merge method with actual data.
     * @throws ReflectionException
     * @covers ::merge
     */
    public function testMergeWithData(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var ItemResult&MockObject $itemToMerge */
        $itemToMerge = $this->createMock(ItemResult::class);
        $itemToMerge->expects($this->atLeastOnce())
                    ->method('getId')
                    ->willReturn($id1);
        $itemToMerge->expects($this->atLeastOnce())
                    ->method('getPriority')
                    ->willReturn(21);
        $itemToMerge->expects($this->once())
                    ->method('getRecipes')
                    ->willReturn([$recipe]);

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->once())
                         ->method('add')
                         ->with($this->identicalTo($recipe));

        $item = new ItemResult();
        $item->setId($id2)
             ->setPriority(100);
        $this->injectProperty($item, 'recipes', $recipeCollection);

        $item->merge($itemToMerge);

        $this->assertSame($id1, $this->extractProperty($item, 'id'));
        $this->assertSame(21, $this->extractProperty($item, 'priority'));
    }

    /**
     * Tests the merge method without actual data.
     * @throws ReflectionException
     * @covers ::merge
     */
    public function testMergeWithoutData(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);

        /* @var ItemResult&MockObject $itemToMerge */
        $itemToMerge = $this->createMock(ItemResult::class);
        $itemToMerge->expects($this->atLeastOnce())
                    ->method('getId')
                    ->willReturn(null);
        $itemToMerge->expects($this->atLeastOnce())
                    ->method('getPriority')
                    ->willReturn(100);
        $itemToMerge->expects($this->once())
                    ->method('getRecipes')
                    ->willReturn([]);

        /* @var RecipeCollection&MockObject $recipeCollection */
        $recipeCollection = $this->createMock(RecipeCollection::class);
        $recipeCollection->expects($this->never())
                         ->method('add');

        $item = new ItemResult();
        $item->setId($id1)
             ->setPriority(21);
        $this->injectProperty($item, 'recipes', $recipeCollection);

        $item->merge($itemToMerge);

        $this->assertSame($id1, $this->extractProperty($item, 'id'));
        $this->assertSame(21, $this->extractProperty($item, 'priority'));
    }
}
