<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search;

use FactorioItemBrowser\Api\Search\Entity\Config;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\SearchManager;
use FactorioItemBrowser\Api\Search\SearchManagerFactory;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use FactorioItemBrowser\Api\Search\Service\FetcherService;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the SearchManagerFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\SearchManagerFactory
 */
class SearchManagerFactoryTest extends TestCase
{
    /**
     * Tests the invoking.
     * @throws ReflectionException
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        /* @var Config&MockObject $config */
        $config = $this->createMock(Config::class);
        $config->expects($this->once())
               ->method('getMaxSearchResults')
               ->willReturn(42);

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(4))
                  ->method('get')
                  ->withConsecutive(
                      [$this->identicalTo(CachedSearchResultService::class)],
                      [$this->identicalTo(Config::class)],
                      [$this->identicalTo(FetcherService::class)],
                      [$this->identicalTo(QueryParser::class)]
                  )
                  ->willReturnOnConsecutiveCalls(
                      $this->createMock(CachedSearchResultService::class),
                      $config,
                      $this->createMock(FetcherService::class),
                      $this->createMock(QueryParser::class)
                  );

        $factory = new SearchManagerFactory();
        $factory($container, SearchManager::class);
    }
}
