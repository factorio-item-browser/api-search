<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
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
        $aliases = ['abc', 'def'];
        $config = [
            ConfigKey::PROJECT => [
                ConfigKey::LIBRARY => [
                    ConfigKey::SERIALIZERS => $aliases,
                ],
            ],
        ];

        $serializers = [
            $this->createMock(SerializerInterface::class),
            $this->createMock(SerializerInterface::class),
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                  ->method('get')
                  ->with($this->identicalTo('config'))
                  ->willReturn($config);

        /* @var SerializerServiceFactory&MockObject $factory */
        $factory = $this->getMockBuilder(SerializerServiceFactory::class)
                        ->setMethods(['createSerializers'])
                        ->getMock();
        $factory->expects($this->once())
                ->method('createSerializers')
                ->with($this->identicalTo($container), $this->identicalTo($aliases))
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
        $aliases = ['abc', 'def'];

        /* @var SerializerInterface&MockObject $serializer1 */
        $serializer1 = $this->createMock(SerializerInterface::class);
        /* @var SerializerInterface&MockObject $serializer2 */
        $serializer2 = $this->createMock(SerializerInterface::class);

        $expectedResult = [$serializer1, $serializer2];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo('abc')],
                      [$this->identicalTo('def')]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $serializer1,
                      $serializer2
                  );

        $factory = new SerializerServiceFactory();
        $result = $this->invokeMethod($factory, 'createSerializers', $container, $aliases);

        $this->assertEquals($expectedResult, $result);
    }
}
