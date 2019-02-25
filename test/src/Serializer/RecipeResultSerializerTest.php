<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeResultResultSerializer class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer
 */
class RecipeResultSerializerTest extends TestCase
{
    /**
     * Tests the getHandledResultClass method.
     * @covers ::getHandledResultClass
     */
    public function testGetHandledResultClass(): void
    {
        $serializer = new RecipeResultSerializer();

        $this->assertSame(RecipeResult::class, $serializer->getHandledResultClass());
    }

    /**
     * Tests the getSerializedType method.
     * @covers ::getSerializedType
     */
    public function testGetSerializedType(): void
    {
        $serializer = new RecipeResultSerializer();

        $this->assertSame(SerializedResultType::RECIPE, $serializer->getSerializedType());
    }

    /**
     * Provides the data for the serialize test.
     * @return array
     */
    public function provideSerialize(): array
    {
        $recipe1 = new RecipeResult();
        $recipe1->setNormalRecipeId(42)
                ->setExpensiveRecipeId(1337);

        $recipe2 = new RecipeResult();
        $recipe2->setNormalRecipeId(42)
                ->setExpensiveRecipeId(0);

        $recipe3 = new RecipeResult();
        $recipe3->setNormalRecipeId(0)
                ->setExpensiveRecipeId(1337);

        $recipe4 = new RecipeResult();
        $recipe4->setNormalRecipeId(50)
                ->setExpensiveRecipeId(0);

        $recipe5 = new RecipeResult();
        $recipe5->setNormalRecipeId(0)
                ->setExpensiveRecipeId(50);

        $recipe6 = new RecipeResult();
        $recipe6->setNormalRecipeId(0)
                ->setExpensiveRecipeId(0);

        return [
            [$recipe1, '42+1337'],
            [$recipe2, '42'],
            [$recipe3, '+1337'],
            [$recipe4, '50'],
            [$recipe5, '+50'],
            [$recipe6, ''],
        ];
    }

    /**
     * Tests the serialize method.
     * @param RecipeResult $recipe
     * @param string $expectedResult
     * @covers ::serialize
     * @dataProvider provideSerialize
     */
    public function testSerialize(RecipeResult $recipe, string $expectedResult): void
    {
        $serializer = new RecipeResultSerializer();
        $result = $serializer->serialize($recipe);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Provides the data for the unserialize test.
     * @return array
     */
    public function provideUnserialize(): array
    {
        $recipe1 = new RecipeResult();
        $recipe1->setNormalRecipeId(42)
                ->setExpensiveRecipeId(1337);

        $recipe2 = new RecipeResult();
        $recipe2->setNormalRecipeId(42)
                ->setExpensiveRecipeId(0);

        $recipe3 = new RecipeResult();
        $recipe3->setNormalRecipeId(0)
                ->setExpensiveRecipeId(1337);

        $recipe4 = new RecipeResult();
        $recipe4->setNormalRecipeId(50)
                ->setExpensiveRecipeId(0);

        $recipe5 = new RecipeResult();
        $recipe5->setNormalRecipeId(0)
                ->setExpensiveRecipeId(50);

        $recipe6 = new RecipeResult();
        $recipe6->setNormalRecipeId(0)
                ->setExpensiveRecipeId(0);

        return [
            ['42+1337', $recipe1],
            ['42', $recipe2],
            ['+1337', $recipe3],
            ['50', $recipe4],
            ['+50', $recipe5],
            ['', $recipe6],
        ];
    }

    /**
     * Tests the unserialize method.
     * @param string $serializedResult
     * @param RecipeResult $expectedResult
     * @covers ::unserialize
     * @dataProvider provideUnserialize
     */
    public function testUnserialize(string $serializedResult, RecipeResult $expectedResult): void
    {
        $serializer = new RecipeResultSerializer();
        $result = $serializer->unserialize($serializedResult);

        $this->assertEquals($expectedResult, $result);
    }
}
