<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManager;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManagerFactory;
use FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the FetcherManagerFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\FetcherManagerFactory
 */
class FetcherManagerFactoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the invoking.
     * @throws ReflectionException
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $fetchers = [
            $this->createMock(FetcherInterface::class),
            $this->createMock(FetcherInterface::class),
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);

        /* @var FetcherManagerFactory&MockObject $factory */
        $factory = $this->getMockBuilder(FetcherManagerFactory::class)
                        ->setMethods(['createFetchers'])
                        ->getMock();
        $factory->expects($this->once())
                ->method('createFetchers')
                ->with($this->identicalTo($container))
                ->willReturn($fetchers);

        $factory($container, FetcherManager::class);
    }

    /**
     * Tests the createFetchers method.
     * @throws ReflectionException
     * @covers ::createFetchers
     */
    public function testCreateFetchers(): void
    {
        /* @var ItemFetcher&MockObject $itemFetcher */
        $itemFetcher = $this->createMock(ItemFetcher::class);
        /* @var MissingItemIdFetcher&MockObject $missingItemIdFetcher */
        $missingItemIdFetcher = $this->createMock(MissingItemIdFetcher::class);

        $expectedResult = [
            $itemFetcher,
            $missingItemIdFetcher
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo(ItemFetcher::class)],
                      [$this->identicalTo(MissingItemIdFetcher::class)]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $itemFetcher,
                      $missingItemIdFetcher
                  );

        $factory = new FetcherManagerFactory();
        $result = $this->invokeMethod($factory, 'createFetchers', $container);

        $this->assertEquals($expectedResult, $result);
    }
}
