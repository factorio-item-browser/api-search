<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use DateTime;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Entity\Config;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultServiceFactory;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the CachedSearchResultServiceFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Service\CachedSearchResultServiceFactory
 */
class CachedSearchResultServiceFactoryTest extends TestCase
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
               ->method('getMaxCacheAge')
               ->willReturn($this->createMock(DateTime::class));

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(3))
                   ->method('get')
                   ->withConsecutive(
                       [$this->identicalTo(CachedSearchResultRepository::class)],
                       [$this->identicalTo(Config::class)],
                       [$this->identicalTo(SerializerService::class)]
                   )
                   ->willReturnOnConsecutiveCalls(
                       $this->createMock(CachedSearchResultRepository::class),
                       $config,
                       $this->createMock(SerializerService::class)
                   );

        $factory = new CachedSearchResultServiceFactory();
        $factory($container, CachedSearchResultService::class);
    }
}
