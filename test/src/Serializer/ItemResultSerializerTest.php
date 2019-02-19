<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ItemResultSerializer class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer
 */
class ItemResultSerializerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked recipe result serializer.
     * @var RecipeResultSerializer&MockObject
     */
    protected $recipeResultSerializer;

    /**
     * Sets up the test case.
     * @throws ReflectionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->recipeResultSerializer = $this->createMock(RecipeResultSerializer::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $serializer = new ItemResultSerializer($this->recipeResultSerializer);

        $this->assertSame($this->recipeResultSerializer, $this->extractProperty($serializer, 'recipeResultSerializer'));
    }

    /**
     * Tests the getHandledResultClass method.
     * @covers ::getHandledResultClass
     */
    public function testGetHandledResultClass(): void
    {
        $serializer = new ItemResultSerializer($this->recipeResultSerializer);

        $this->assertSame(ItemResult::class, $serializer->getHandledResultClass());
    }

    /**
     * Tests the getSerializedType method.
     * @covers ::getSerializedType
     */
    public function testGetSerializedType(): void
    {
        $serializer = new ItemResultSerializer($this->recipeResultSerializer);

        $this->assertSame(SerializedResultType::ITEM, $serializer->getSerializedType());
    }

    /**
     * Tests the serialize method.
     * @throws ReflectionException
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        $itemId = 42;
        $recipes = [
            $this->createMock(RecipeResult::class),
            $this->createMock(RecipeResult::class),
        ];
        $serializedRecipes = ['abc', 'def'];
        $expectedResult = '42,abc,def';

        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        $item->expects($this->once())
             ->method('getId')
             ->willReturn($itemId);
        $item->expects($this->once())
             ->method('getRecipes')
             ->willReturn($recipes);

        /* @var ItemResultSerializer&MockObject $serializer */
        $serializer = $this->getMockBuilder(ItemResultSerializer::class)
                           ->setMethods(['serializeRecipes'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $serializer->expects($this->once())
                   ->method('serializeRecipes')
                   ->with($this->identicalTo($recipes))
                   ->willReturn($serializedRecipes);

        $result = $serializer->serialize($item);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the serializeRecipes method.
     * @throws ReflectionException
     * @covers ::serializeRecipes
     */
    public function testSerializeRecipes(): void
    {
        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $serializedRecipe1 = 'abc';
        $serializedRecipe2 = 'def';

        $recipes = [$recipe1, $recipe2];
        $expectedResult = [$serializedRecipe1, $serializedRecipe2];

        $this->recipeResultSerializer->expects($this->exactly(2))
                                     ->method('serialize')
                                     ->withConsecutive(
                                         [$this->identicalTo($recipe1)],
                                         [$this->identicalTo($recipe2)]
                                     )
                                     ->willReturnOnConsecutiveCalls(
                                         $serializedRecipe1,
                                         $serializedRecipe2
                                     );

        $serializer = new ItemResultSerializer($this->recipeResultSerializer);
        $result = $this->invokeMethod($serializer, 'serializeRecipes', $recipes);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Provides the data for the unserialize test.
     * @return array
     */
    public function provideUnserialize(): array
    {
        return [
            ['42,abc,def', 42, ['abc', 'def']],
            ['42', 42, []],
            ['', 0, []],
        ];
    }

    /**
     * Tests the unserialize method.
     * @param string $serializedResult
     * @param int $expectedItemId
     * @param array|string[] $expectedSerializedRecipes
     * @throws ReflectionException
     * @covers ::unserialize
     * @dataProvider provideUnserialize
     */
    public function testUnserialize(
        string $serializedResult,
        int $expectedItemId,
        array $expectedSerializedRecipes
    ): void {
        $expectedResult = new ItemResult();
        $expectedResult->setId($expectedItemId);

        /* @var ItemResultSerializer&MockObject $serializer */
        $serializer = $this->getMockBuilder(ItemResultSerializer::class)
                           ->setMethods(['unserializeRecipes'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $serializer->expects($this->once())
                   ->method('unserializeRecipes')
                   ->with($this->equalTo($expectedSerializedRecipes), $this->isInstanceOf(ItemResult::class));

        $result = $serializer->unserialize($serializedResult);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the unserializeRecipes method.
     * @throws ReflectionException
     * @covers ::unserializeRecipes
     */
    public function testUnserializeRecipes(): void
    {
        $serializedRecipe1 = 'abc';
        $serializedRecipe2 = 'def';

        /* @var RecipeResult&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeResult::class);

        $serializedRecipes = [$serializedRecipe1, $serializedRecipe2];

        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        $item->expects($this->exactly(2))
             ->method('addRecipe')
             ->withConsecutive(
                 [$this->identicalTo($recipe1)],
                 [$this->identicalTo($recipe2)]
             );

        $this->recipeResultSerializer->expects($this->exactly(2))
                                     ->method('unserialize')
                                     ->withConsecutive(
                                         [$this->identicalTo($serializedRecipe1)],
                                         [$this->identicalTo($serializedRecipe2)]
                                     )
                                     ->willReturnOnConsecutiveCalls(
                                         $recipe1,
                                         $recipe2
                                     );

        $serializer = new ItemResultSerializer($this->recipeResultSerializer);
        $this->invokeMethod($serializer, 'unserializeRecipes', $serializedRecipes, $item);
    }
}