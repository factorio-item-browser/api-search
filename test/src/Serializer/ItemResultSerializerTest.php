<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;
use FactorioItemBrowser\Api\Search\Serializer\DataReader;
use FactorioItemBrowser\Api\Search\Serializer\DataWriter;
use FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the ItemResultSerializer class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer
 */
class ItemResultSerializerTest extends TestCase
{
    /** @var RecipeResultSerializer&MockObject */
    private RecipeResultSerializer $recipeResultSerializer;

    public function setUp(): void
    {
        $this->recipeResultSerializer = $this->createMock(RecipeResultSerializer::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return ItemResultSerializer&MockObject
     */
    private function createInstance(array $mockedMethods = []): ItemResultSerializer
    {
        return $this->getMockBuilder(ItemResultSerializer::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->recipeResultSerializer,
                    ])
                    ->getMock();
    }


    public function testMeta(): void
    {
        $instance = $this->createInstance();

        $this->assertSame(ItemResult::class, $instance->getHandledResultClass());
        $this->assertSame(SerializedResultType::ITEM, $instance->getSerializedType());
    }

    /**
     * @throws WriterException
     */
    public function testSerialize(): void
    {
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe2 = $this->createMock(RecipeResult::class);
        $expectedData = (string) hex2bin('1ce5dc3391b24f698680639d702d9f5602');

        $item = new ItemResult();
        $item->setId(Uuid::fromString('1ce5dc33-91b2-4f69-8680-639d702d9f56'))
             ->addRecipe($recipe1)
             ->addRecipe($recipe2);

        $writer = new DataWriter();

        $this->recipeResultSerializer->expects($this->exactly(2))
                                     ->method('serialize')
                                     ->withConsecutive(
                                         [$this->identicalTo($writer), $this->identicalTo($recipe1)],
                                         [$this->identicalTo($writer), $this->identicalTo($recipe2)],
                                     );

        $instance = $this->createInstance();
        $instance->serialize($writer, $item);

        $this->assertSame($expectedData, $writer->toString());
    }

    public function testSerializeWithException(): void
    {
        $item = new ItemResult();
        $writer = new DataWriter();

        $this->expectException(WriterException::class);

        $instance = $this->createInstance();
        $instance->serialize($writer, $item);
    }

    /**
     * @throws ReaderException
     */
    public function testUnserialize(): void
    {
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe2 = $this->createMock(RecipeResult::class);
        $data = (string) hex2bin('1ce5dc3391b24f698680639d702d9f5602');

        $expectedResult = new ItemResult();
        $expectedResult->setId(Uuid::fromString('1ce5dc33-91b2-4f69-8680-639d702d9f56'))
                       ->addRecipe($recipe1)
                       ->addRecipe($recipe2);

        $reader = new DataReader($data);

        $this->recipeResultSerializer->expects($this->exactly(2))
                                     ->method('unserialize')
                                     ->with($this->identicalTo($reader))
                                     ->willReturnOnConsecutiveCalls(
                                         $recipe1,
                                         $recipe2
                                     );

        $instance = $this->createInstance();
        $result = $instance->unserialize($reader);

        $this->assertEquals($expectedResult, $result);
    }
}
