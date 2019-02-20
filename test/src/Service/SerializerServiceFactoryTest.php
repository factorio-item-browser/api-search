<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use FactorioItemBrowser\Api\Search\Service\SerializerServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the SerializerManagerFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Service\SerializerServiceFactory
 */
class SerializerServiceFactoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the invoking.
     * @throws ReflectionException
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $serializers = [
            $this->createMock(SerializerInterface::class),
            $this->createMock(SerializerInterface::class),
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);

        /* @var SerializerServiceFactory&MockObject $factory */
        $factory = $this->getMockBuilder(SerializerServiceFactory::class)
                        ->setMethods(['createSerializers'])
                        ->getMock();
        $factory->expects($this->once())
                ->method('createSerializers')
                ->with($this->identicalTo($container))
                ->willReturn($serializers);

        $factory($container, SerializerService::class);
    }

    /**
     * Tests the createSerializers method.
     * @throws ReflectionException
     * @covers ::createSerializers
     */
    public function testCreateSerializers(): void
    {
        /* @var ItemResultSerializer&MockObject $itemResultSerializer */
        $itemResultSerializer = $this->createMock(ItemResultSerializer::class);
        /* @var RecipeResultSerializer&MockObject $recipeResultSerializer */
        $recipeResultSerializer = $this->createMock(RecipeResultSerializer::class);

        $expectedResult = [$itemResultSerializer, $recipeResultSerializer];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo(ItemResultSerializer::class)],
                      [$this->identicalTo(RecipeResultSerializer::class)]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $itemResultSerializer,
                      $recipeResultSerializer
                  );

        $factory = new SerializerServiceFactory();
        $result = $this->invokeMethod($factory, 'createSerializers', $container);

        $this->assertEquals($expectedResult, $result);
    }
}
