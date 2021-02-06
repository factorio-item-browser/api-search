<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;
use FactorioItemBrowser\Api\Search\Serializer\DataReader;
use FactorioItemBrowser\Api\Search\Serializer\DataWriter;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the SerializerService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Service\SerializerService
 */
class SerializerServiceTest extends TestCase
{
    /** @var array<SerializerInterface<ResultInterface>> */
    private array $serializers = [];

    /**
     * @param array<string> $mockedMethods
     * @return SerializerService&MockObject
     */
    private function createInstance(array $mockedMethods = []): SerializerService
    {
        return $this->getMockBuilder(SerializerService::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->serializers,
                    ])
                    ->getMock();
    }

    /**
     * @throws WriterException
     */
    public function testSerialize(): void
    {
        $searchResult1 = new ItemResult();
        $searchResult1->setId(Uuid::fromString('14c535ca-a47b-4f71-be3f-5835ef8cedeb'));
        $searchResult2 = new RecipeResult();
        $searchResult2->setNormalRecipeId(Uuid::fromString('26d62a7e-48e5-467a-b5d4-3b28ac279d84'));
        $searchResult3 = new ItemResult();
        $searchResult3->setId(Uuid::fromString('3b7b48a0-4e64-4117-8a23-b6675d0d5926'));

        $expectedResult = (string) hex2bin(implode('', [
            '0114c535caa47b4f71be3f5835ef8cedeb',
            '0226d62a7e48e5467ab5d43b28ac279d84',
            '013b7b48a04e6441178a23b6675d0d5926',
        ]));

        $serializer1 = $this->createMock(SerializerInterface::class);
        $serializer1->expects($this->any())
                    ->method('getHandledResultClass')
                    ->willReturn(ItemResult::class);
        $serializer1->expects($this->any())
                    ->method('getSerializedType')
                    ->willReturn(SerializedResultType::ITEM);
        $serializer1->expects($this->exactly(2))
                    ->method('serialize')
                    ->withConsecutive(
                        [
                            new Callback(function (DataWriter $writer) use ($searchResult1): bool {
                                $this->assertNotNull($searchResult1->getId());
                                $writer->writeId($searchResult1->getId());
                                return true;
                            }),
                            $this->identicalTo($searchResult1),
                        ],
                        [
                            new Callback(function (DataWriter $writer) use ($searchResult3): bool {
                                $this->assertNotNull($searchResult3->getId());
                                $writer->writeId($searchResult3->getId());
                                return true;
                            }),
                            $this->identicalTo($searchResult3),
                        ],
                    );

        $serializer2 = $this->createMock(SerializerInterface::class);
        $serializer2->expects($this->any())
                    ->method('getHandledResultClass')
                    ->willReturn(RecipeResult::class);
        $serializer2->expects($this->any())
                    ->method('getSerializedType')
                    ->willReturn(SerializedResultType::RECIPE);
        $serializer2->expects($this->once())
                    ->method('serialize')
                    ->with(
                        new Callback(function (DataWriter $writer) use ($searchResult2): bool {
                            $this->assertNotNull($searchResult2->getNormalRecipeId());
                            $writer->writeId($searchResult2->getNormalRecipeId());
                            return true;
                        }),
                        $this->identicalTo($searchResult2),
                    );

        $this->serializers = [$serializer1, $serializer2];

        $searchResults = new PaginatedResultCollection();
        $searchResults->add($searchResult1)
                      ->add($searchResult2)
                      ->add($searchResult3);

        $instance = $this->createInstance();
        $result = $instance->serialize($searchResults);

        $this->assertSame($expectedResult, $result);
    }

    public function testSerializeWithException(): void
    {
        $searchResult1 = new ItemResult();
        $searchResult1->setId(Uuid::fromString('14c535ca-a47b-4f71-be3f-5835ef8cedeb'));

        $searchResults = new PaginatedResultCollection();
        $searchResults->add($searchResult1);

        $this->expectException(WriterException::class);

        $instance = $this->createInstance();
        $instance->serialize($searchResults);
    }

    /**
     * @throws ReaderException
     */
    public function testUnserialize(): void
    {
        $serializedResults = (string) hex2bin(implode('', [
            '0114c535caa47b4f71be3f5835ef8cedeb',
            '0226d62a7e48e5467ab5d43b28ac279d84',
            '013b7b48a04e6441178a23b6675d0d5926',
        ]));

        $expectedSearchResult1 = new ItemResult();
        $expectedSearchResult1->setId(Uuid::fromString('14c535ca-a47b-4f71-be3f-5835ef8cedeb'));
        $expectedSearchResult2 = new RecipeResult();
        $expectedSearchResult2->setNormalRecipeId(Uuid::fromString('26d62a7e-48e5-467a-b5d4-3b28ac279d84'));
        $expectedSearchResult3 = new ItemResult();
        $expectedSearchResult3->setId(Uuid::fromString('3b7b48a0-4e64-4117-8a23-b6675d0d5926'));

        $serializer1 = $this->createMock(SerializerInterface::class);
        $serializer1->expects($this->any())
                    ->method('getHandledResultClass')
                    ->willReturn(ItemResult::class);
        $serializer1->expects($this->any())
                    ->method('getSerializedType')
                    ->willReturn(SerializedResultType::ITEM);
        $serializer1->expects($this->exactly(2))
                    ->method('unserialize')
                    ->willReturnCallback(function (DataReader $reader): ResultInterface {
                        $result = new ItemResult();
                        $result->setId($reader->readId());
                        return $result;
                    });

        $serializer2 = $this->createMock(SerializerInterface::class);
        $serializer2->expects($this->any())
                    ->method('getHandledResultClass')
                    ->willReturn(RecipeResult::class);
        $serializer2->expects($this->any())
                    ->method('getSerializedType')
                    ->willReturn(SerializedResultType::RECIPE);
        $serializer2->expects($this->once())
                    ->method('unserialize')
                    ->willReturnCallback(function (DataReader $reader): ResultInterface {
                        $result = new RecipeResult();
                        $result->setNormalRecipeId($reader->readId());
                        return $result;
                    });

        $this->serializers = [$serializer1, $serializer2];

        $instance = $this->createInstance();
        $result = $instance->unserialize($serializedResults);

        $this->assertSame(3, $result->count());
        $this->assertEquals($expectedSearchResult1, $result->getResults(0, 1)[0]);
        $this->assertEquals($expectedSearchResult2, $result->getResults(1, 1)[0]);
        $this->assertEquals($expectedSearchResult3, $result->getResults(2, 1)[0]);
    }

    public function testUnserializeWithException(): void
    {
        $serializedResults = (string) hex2bin(implode('', [
            '0114c535caa47b4f71be3f5835ef8cedeb',
            '0226d62a7e48e5467ab5d43b28ac279d84',
            '013b7b48a04e6441178a23b6675d0d5926',
        ]));

        $this->expectException(ReaderException::class);

        $instance = $this->createInstance();
        $instance->unserialize($serializedResults);
    }
}
