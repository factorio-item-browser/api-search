<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;
use FactorioItemBrowser\Api\Search\Serializer\SerializerManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the SerializerManager class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Serializer\SerializerManager
 */
class SerializerManagerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        /* @var SerializerInterface&MockObject $serializer1 */
        $serializer1 = $this->createMock(SerializerInterface::class);
        $serializer1->expects($this->once())
                    ->method('getHandledResultClass')
                    ->willReturn('abc');
        $serializer1->expects($this->once())
                    ->method('getSerializedType')
                    ->willReturn('z');

        /* @var SerializerInterface&MockObject $serializer2 */
        $serializer2 = $this->createMock(SerializerInterface::class);
        $serializer2->expects($this->once())
                    ->method('getHandledResultClass')
                    ->willReturn('def');
        $serializer2->expects($this->once())
                    ->method('getSerializedType')
                    ->willReturn('y');

        $serializers = [$serializer1, $serializer2];
        $expectedSerializersByClassName = [
            'abc' => $serializer1,
            'def' => $serializer2,
        ];
        $expectedSerializersByType = [
            'z' => $serializer1,
            'y' => $serializer2,
        ];

        $manager = new SerializerManager($serializers);

        $this->assertSame($expectedSerializersByClassName, $this->extractProperty($manager, 'serializersByClassName'));
        $this->assertSame($expectedSerializersByType, $this->extractProperty($manager, 'serializersByType'));
    }

    /**
     * Tests the serialize method.
     * @throws ReflectionException
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        /* @var ResultInterface&MockObject $searchResult1 */
        $searchResult1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $searchResult2 */
        $searchResult2 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $searchResult3 */
        $searchResult3 = $this->createMock(ResultInterface::class);

        $serializedResult1 = 'abc';
        $serializedResult2 = '';
        $serializedResult3 = 'def';

        $searchResults = [$searchResult1, $searchResult2, $searchResult3];
        $expectedResult = 'abc|def';

        /* @var PaginatedResultCollection&MockObject $paginatedResultCollection */
        $paginatedResultCollection = $this->createMock(PaginatedResultCollection::class);
        $paginatedResultCollection->expects($this->once())
                                  ->method('count')
                                  ->willReturn(2);
        $paginatedResultCollection->expects($this->once())
                                  ->method('getResults')
                                  ->with($this->identicalTo(0), $this->identicalTo(2))
                                  ->willReturn($searchResults);

        /* @var SerializerManager&MockObject $manager */
        $manager = $this->getMockBuilder(SerializerManager::class)
                        ->setMethods(['serializeResult'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $manager->expects($this->exactly(3))
                ->method('serializeResult')
                ->withConsecutive(
                    [$this->identicalTo($searchResult1)],
                    [$this->identicalTo($searchResult2)],
                    [$this->identicalTo($searchResult3)]
                )
                ->willReturnOnConsecutiveCalls(
                    $serializedResult1,
                    $serializedResult2,
                    $serializedResult3
                );

        $result = $manager->serialize($paginatedResultCollection);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the serializeResult method with a matching serializer.
     * @throws ReflectionException
     * @covers ::serializeResult
     */
    public function testSerializeResultWithMatchingSerializer(): void
    {
        $mockedClassName = 'ResultInterfaceMock';
        $serializedType = 'a';
        $searializedResult = 'bc';
        $expectedResult = 'abc';

        /* @var ResultInterface&MockObject $searchResult */
        $searchResult = $this->getMockBuilder(ResultInterface::class)
                             ->setMockClassName($mockedClassName)
                             ->getMockForAbstractClass();

        /* @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
                   ->method('getSerializedType')
                   ->willReturn($serializedType);
        $serializer->expects($this->once())
                   ->method('serialize')
                   ->with($searchResult)
                   ->willReturn($searializedResult);

        $manager = new SerializerManager([]);
        $this->injectProperty($manager, 'serializersByClassName', [$mockedClassName => $serializer]);

        $result = $this->invokeMethod($manager, 'serializeResult', $searchResult);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the serializeResult method without a matching serializer.
     * @throws ReflectionException
     * @covers ::serializeResult
     */
    public function testSerializeResultWithoutSerializer(): void
    {
        $expectedResult = '';

        /* @var ResultInterface&MockObject $searchResult */
        $searchResult = $this->createMock(ResultInterface::class);

        $manager = new SerializerManager([]);
        $result = $this->invokeMethod($manager, 'serializeResult', $searchResult);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the unserialize method.
     * @throws ReflectionException
     * @covers ::unserialize
     */
    public function testUnserialize(): void
    {
        $serializedResults = 'abc|def|ghi';
        $serializedResult1 = 'abc';
        $serializedResult2 = 'def';
        $serializedResult3 = 'ghi';
        
        /* @var ResultInterface&MockObject $searchResult1 */
        $searchResult1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $searchResult2 */
        $searchResult2 = $this->createMock(ResultInterface::class);
        
        /* @var PaginatedResultCollection&MockObject $resultCollection */
        $resultCollection = $this->createMock(PaginatedResultCollection::class);
        $resultCollection->expects($this->exactly(2))
                         ->method('add')
                         ->withConsecutive(
                             [$this->identicalTo($searchResult1)],
                             [$this->identicalTo($searchResult2)]
                         );

        /* @var SerializerManager&MockObject $manager */
        $manager = $this->getMockBuilder(SerializerManager::class)
                        ->setMethods(['createResultCollection', 'unserializeResult'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $manager->expects($this->once())
                ->method('createResultCollection')
                ->willReturn($resultCollection);
        $manager->expects($this->exactly(3))
                ->method('unserializeResult')
                ->withConsecutive(
                    [$this->identicalTo($serializedResult1)],
                    [$this->identicalTo($serializedResult2)],
                    [$this->identicalTo($serializedResult3)]
                )
                ->willReturnOnConsecutiveCalls(
                    $searchResult1,
                    null,
                    $searchResult2
                );

        $result = $manager->unserialize($serializedResults);

        $this->assertSame($resultCollection, $result);
    }

    /**
     * Tests the createResultCollection method.
     * @throws ReflectionException
     * @covers ::createResultCollection
     */
    public function testCreateResultCollection(): void
    {
        $expectedResult = new PaginatedResultCollection();

        $manager = new SerializerManager([]);
        $result = $this->invokeMethod($manager, 'createResultCollection');

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the unserializeResult method with a matching serializer.
     * @throws ReflectionException
     * @covers ::unserializeResult
     */
    public function testUnserializeResultWithSerializer(): void
    {
        $serializedResult = 'abc';
        $expectedSerializedResult = 'bc';

        /* @var ResultInterface&MockObject $searchResult */
        $searchResult = $this->createMock(ResultInterface::class);

        /* @var SerializerInterface&MockObject $serializer */
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
                   ->method('unserialize')
                   ->with($this->identicalTo($expectedSerializedResult))
                   ->willReturn($searchResult);

        $manager = new SerializerManager([]);
        $this->injectProperty($manager, 'serializersByType', ['a' => $serializer]);

        $result = $this->invokeMethod($manager, 'unserializeResult', $serializedResult);
        $this->assertSame($searchResult, $result);
    }

    /**
     * Tests the unserializeResult method without a matching serializer.
     * @throws ReflectionException
     * @covers ::unserializeResult
     */
    public function testUnserializeResultWithoutSerializer(): void
    {
        $serializedResult = 'abc';

        $manager = new SerializerManager([]);

        $result = $this->invokeMethod($manager, 'unserializeResult', $serializedResult);
        $this->assertNull($result);
    }
}
