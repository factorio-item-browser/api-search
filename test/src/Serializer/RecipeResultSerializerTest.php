<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
     * @return array<mixed>
     */
    public function provideSerialize(): array
    {
        $id1 = Uuid::fromString('40718ef3-3d81-4c6f-ac42-650d4c38d226');
        $id2 = Uuid::fromString('79c6ee59-57b3-4fe1-a766-10c1454cdc8a');

        $recipe1 = new RecipeResult();
        $recipe1->setNormalRecipeId($id1)
                ->setExpensiveRecipeId($id2);

        $recipe2 = new RecipeResult();
        $recipe2->setNormalRecipeId($id1)
                ->setExpensiveRecipeId(null);

        $recipe3 = new RecipeResult();
        $recipe3->setNormalRecipeId(null)
                ->setExpensiveRecipeId($id2);

        $recipe4 = new RecipeResult();
        $recipe4->setNormalRecipeId(null)
                ->setExpensiveRecipeId(null);

        return [
            [$recipe1, '40718ef3-3d81-4c6f-ac42-650d4c38d226+79c6ee59-57b3-4fe1-a766-10c1454cdc8a'],
            [$recipe2, '40718ef3-3d81-4c6f-ac42-650d4c38d226'],
            [$recipe3, '+79c6ee59-57b3-4fe1-a766-10c1454cdc8a'],
            [$recipe4, ''],
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
     * @return array<mixed>
     */
    public function provideUnserialize(): array
    {
        $id1 = Uuid::fromString('40718ef3-3d81-4c6f-ac42-650d4c38d226');
        $id2 = Uuid::fromString('79c6ee59-57b3-4fe1-a766-10c1454cdc8a');

        $recipe1 = new RecipeResult();
        $recipe1->setNormalRecipeId($id1)
                ->setExpensiveRecipeId($id2);

        $recipe2 = new RecipeResult();
        $recipe2->setNormalRecipeId($id1)
                ->setExpensiveRecipeId(null);

        $recipe3 = new RecipeResult();
        $recipe3->setNormalRecipeId(null)
                ->setExpensiveRecipeId($id2);

        $recipe4 = new RecipeResult();
        $recipe4->setNormalRecipeId(null)
                ->setExpensiveRecipeId(null);

        return [
            ['40718ef3-3d81-4c6f-ac42-650d4c38d226+79c6ee59-57b3-4fe1-a766-10c1454cdc8a', $recipe1],
            ['40718ef3-3d81-4c6f-ac42-650d4c38d226', $recipe2],
            ['+79c6ee59-57b3-4fe1-a766-10c1454cdc8a', $recipe3],
            ['', $recipe4],
            ['+', $recipe4],
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
