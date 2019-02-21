<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Service\FetcherService;
use FactorioItemBrowser\Api\Search\Service\FetcherServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the FetcherManagerFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Service\FetcherServiceFactory
 */
class FetcherServiceFactoryTest extends TestCase
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
                    ConfigKey::FETCHERS => $aliases,
                ],
            ],
        ];

        $fetchers = [
            $this->createMock(FetcherInterface::class),
            $this->createMock(FetcherInterface::class),
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                  ->method('get')
                  ->with($this->identicalTo('config'))
                  ->willReturn($config);


        /* @var FetcherServiceFactory&MockObject $factory */
        $factory = $this->getMockBuilder(FetcherServiceFactory::class)
                        ->setMethods(['createFetchers'])
                        ->getMock();
        $factory->expects($this->once())
                ->method('createFetchers')
                ->with($this->identicalTo($container), $this->identicalTo($aliases))
                ->willReturn($fetchers);

        $factory($container, FetcherService::class);
    }

    /**
     * Tests the createFetchers method.
     * @throws ReflectionException
     * @covers ::createFetchers
     */
    public function testCreateFetchers(): void
    {
        $aliases = ['abc', 'def'];

        /* @var FetcherInterface&MockObject $fetcher1 */
        $fetcher1 = $this->createMock(FetcherInterface::class);
        /* @var FetcherInterface&MockObject $fetcher2 */
        $fetcher2 = $this->createMock(FetcherInterface::class);

        $expectedResult = [$fetcher1, $fetcher2];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo('abc')],
                      [$this->identicalTo('def')]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $fetcher1,
                      $fetcher2
                  );

        $factory = new FetcherServiceFactory();
        $result = $this->invokeMethod($factory, 'createFetchers', $container, $aliases);

        $this->assertEquals($expectedResult, $result);
    }
}
