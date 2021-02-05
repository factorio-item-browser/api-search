<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;
use FactorioItemBrowser\Api\Search\Serializer\DataReader;
use FactorioItemBrowser\Api\Search\Serializer\DataWriter;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the RecipeResultResultSerializer class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer
 */
class RecipeResultSerializerTest extends TestCase
{
    /**
     * @param array<string> $mockedMethods
     * @return RecipeResultSerializer&MockObject
     */
    private function createInstance(array $mockedMethods = []): RecipeResultSerializer
    {
        return $this->getMockBuilder(RecipeResultSerializer::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->getMock();
    }

    public function testMeta(): void
    {
        $instance = $this->createInstance();

        $this->assertSame(RecipeResult::class, $instance->getHandledResultClass());
        $this->assertSame(SerializedResultType::RECIPE, $instance->getSerializedType());
    }

    /**
     * @return array<mixed>
     */
    public function provideSerialize(): array
    {
        $id1 = Uuid::fromString('186bfc88-40bb-4649-a588-1c10c8c092cd');
        $id2 = Uuid::fromString('275e1192-816f-45e5-8de9-f508f71328b6');

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
            [$recipe1, false, (string) hex2bin('01186bfc8840bb4649a5881c10c8c092cd275e1192816f45e58de9f508f71328b6')],
            [$recipe2, false, (string) hex2bin('02186bfc8840bb4649a5881c10c8c092cd')],
            [$recipe3, false, (string) hex2bin('03275e1192816f45e58de9f508f71328b6')],
            [$recipe4, true, ''],
        ];
    }

    /**
     * @param RecipeResult $recipe
     * @param bool $expectException
     * @param string $expectedData
     * @dataProvider provideSerialize
     */
    public function testSerialize(RecipeResult $recipe, bool $expectException, string $expectedData): void
    {
        if ($expectException) {
            $this->expectException(WriterException::class);
        }

        $writer = new DataWriter();

        $instance = $this->createInstance();
        $instance->serialize($writer, $recipe);

        $this->assertSame($expectedData, $writer->toString());
    }

    /**
     * @return array<mixed>
     */
    public function provideUnserialize(): array
    {
        $id1 = Uuid::fromString('186bfc88-40bb-4649-a588-1c10c8c092cd');
        $id2 = Uuid::fromString('275e1192-816f-45e5-8de9-f508f71328b6');

        $recipe1 = new RecipeResult();
        $recipe1->setNormalRecipeId($id1)
                ->setExpensiveRecipeId($id2);

        $recipe2 = new RecipeResult();
        $recipe2->setNormalRecipeId($id1)
                ->setExpensiveRecipeId(null);

        $recipe3 = new RecipeResult();
        $recipe3->setNormalRecipeId(null)
                ->setExpensiveRecipeId($id2);

        return [
            [(string) hex2bin('01186bfc8840bb4649a5881c10c8c092cd275e1192816f45e58de9f508f71328b6'), false, $recipe1],
            [(string) hex2bin('02186bfc8840bb4649a5881c10c8c092cd'), false, $recipe2],
            [(string) hex2bin('03275e1192816f45e58de9f508f71328b6'), false, $recipe3],
            [(string) hex2bin('043db86a82ca6f4878844dfd6f37ad40c9'), true, null],
            ['', true, null],
        ];
    }

    /**
     * @dataProvider provideUnserialize
     * @param string $data
     * @param bool $expectException
     * @param RecipeResult|null $expectedResult
     */
    public function testUnserialize(string $data, bool $expectException, ?RecipeResult $expectedResult): void
    {
        if ($expectException) {
            $this->expectException(ReaderException::class);
        }

        $reader = new DataReader($data);

        $instance = $this->createInstance();
        $result = $instance->unserialize($reader);

        $this->assertEquals($expectedResult, $result);
        $this->assertFalse($reader->hasUnreadData());
    }
}
