<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManager;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManagerFactory;
use FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher;
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
        /* @var RecipeFetcher&MockObject $recipeFetcher */
        $recipeFetcher = $this->createMock(RecipeFetcher::class);
        /* @var MissingItemIdFetcher&MockObject $missingItemIdFetcher */
        $missingItemIdFetcher = $this->createMock(MissingItemIdFetcher::class);
        /* @var MissingRecipeIdFetcher&MockObject $missingRecipeIdFetcher */
        $missingRecipeIdFetcher = $this->createMock(MissingRecipeIdFetcher::class);

        $expectedResult = [
            $itemFetcher,
            $recipeFetcher,
            $missingItemIdFetcher,
            $missingRecipeIdFetcher,
        ];

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(4))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo(ItemFetcher::class)],
                      [$this->identicalTo(RecipeFetcher::class)],
                      [$this->identicalTo(MissingItemIdFetcher::class)],
                      [$this->identicalTo(MissingRecipeIdFetcher::class)]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $itemFetcher,
                      $recipeFetcher,
                      $missingItemIdFetcher,
                      $missingRecipeIdFetcher
                  );

        $factory = new FetcherManagerFactory();
        $result = $this->invokeMethod($factory, 'createFetchers', $container);

        $this->assertEquals($expectedResult, $result);
    }
}
