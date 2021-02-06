<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the AggregatingResultCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection
 */
class AggregatingResultCollectionTest extends TestCase
{
    public function testItems(): void
    {
        $item1 = new ItemResult();
        $item1->setName('abc');
        $item2 = new ItemResult();
        $item2->setName('def');

        $instance = new AggregatingResultCollection();

        $this->assertEquals([], $instance->getItems());
        $this->assertSame($instance, $instance->addItem($item1));
        $this->assertEquals([$item1], $instance->getItems());

        $instance->addItem($item2);
        $this->assertEquals([$item1, $item2], $instance->getItems());

        $this->assertSame($instance, $instance->removeItem($item1));
        $this->assertEquals([$item2], $instance->getItems());
    }

    public function testRecipes(): void
    {
        $recipe1 = new RecipeResult();
        $recipe1->setName('abc');
        $recipe2 = new RecipeResult();
        $recipe2->setName('def');

        $instance = new AggregatingResultCollection();

        $this->assertEquals([], $instance->getRecipes());
        $this->assertSame($instance, $instance->addRecipe($recipe1));
        $this->assertEquals([$recipe1], $instance->getRecipes());

        $instance->addRecipe($recipe2);
        $this->assertEquals([$recipe1, $recipe2], $instance->getRecipes());

        $this->assertSame($instance, $instance->removeRecipe($recipe1));
        $this->assertEquals([$recipe2], $instance->getRecipes());
    }

    public function testGetMergedResults(): void
    {
        $item1 = new ItemResult();
        $item1->setType('item')
              ->setName('abc')
              ->setPriority(21);

        $item2 = new ItemResult();
        $item2->setType('item')
              ->setName('def')
              ->setPriority(21);

        $item3 = new ItemResult();
        $item3->setType('item')
              ->setName('bcd')
              ->setPriority(42);

        $recipe1 = new RecipeResult();
        $recipe1->setPriority(21)
                ->setName('def');

        $recipe2 = new RecipeResult();
        $recipe2->setPriority(42)
                ->setName('bcd');

        $expectedResult = [$item1, $item2, $recipe1, $item3, $recipe2];

        $instance = new AggregatingResultCollection();
        $instance->addItem($item2)
                 ->addItem($item3)
                 ->addItem($item1)
                 ->addRecipe($recipe2)
                 ->addRecipe($recipe1);

        $result = $instance->getMergedResults();

        $this->assertSame($expectedResult, $result);
    }
}
