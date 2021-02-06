<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ItemResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Entity\Result\ItemResult
 */
class ItemResultTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new ItemResult();

        $this->assertSame('', $instance->getType());
        $this->assertSame('', $instance->getName());
        $this->assertNull($instance->getId());
        $this->assertSame(SearchResultPriority::ANY_MATCH, $instance->getPriority());
    }

    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $instance = new ItemResult();

        $this->assertSame($instance, $instance->setType($type));
        $this->assertSame($type, $instance->getType());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = new ItemResult();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testSetAndGetId(): void
    {
        $id = $this->createMock(UuidInterface::class);
        $instance = new ItemResult();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testRecipes(): void
    {
        $recipe1 = new RecipeResult();
        $recipe1->setName('abc');
        $recipe2 = new RecipeResult();
        $recipe2->setName('def');

        $instance = new ItemResult();

        $result = $instance->addRecipe($recipe1)
                           ->addRecipe($recipe2);
        $this->assertSame($instance, $result);

        $this->assertEquals([$recipe1, $recipe2], $instance->getRecipes());
    }

    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $result = new ItemResult();

        $this->assertSame($result, $result->setPriority($priority));
        $this->assertSame($priority, $result->getPriority());
    }

    public function testMergeWithData(): void
    {
        $id1 = $this->createMock(UuidInterface::class);
        $id2 = $this->createMock(UuidInterface::class);

        $recipe1 = new RecipeResult();
        $recipe1->setName('abc');
        $recipe2 = new RecipeResult();
        $recipe2->setName('abc');

        $itemToMerge = new ItemResult();
        $itemToMerge->setId($id1)
                    ->setPriority(21)
                    ->addRecipe($recipe1);

        $expectedItem = new ItemResult();
        $expectedItem->setId($id1)
                     ->setPriority(21)
                     ->addRecipe($recipe2)
                     ->addRecipe($recipe1);

        $instance = new ItemResult();
        $instance->setId($id2)
                 ->setPriority(100)
                 ->addRecipe($recipe2);

        $instance->merge($itemToMerge);

        $this->assertEquals($expectedItem, $instance);
    }

    public function testMergeWithoutData(): void
    {
        $id1 = $this->createMock(UuidInterface::class);

        $recipe1 = new RecipeResult();
        $recipe1->setName('abc');

        $itemToMerge = new ItemResult();
        $itemToMerge->setPriority(100);

        $expectedItem = new ItemResult();
        $expectedItem->setId($id1)
                     ->setPriority(21)
                     ->addRecipe($recipe1);

        $instance = new ItemResult();
        $instance->setId($id1)
                 ->setPriority(21)
                 ->addRecipe($recipe1);

        $instance->merge($itemToMerge);

        $this->assertEquals($expectedItem, $instance);
    }
}
