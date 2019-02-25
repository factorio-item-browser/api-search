<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use BluePsyduck\Common\Test\ReflectionTrait;
use DateTimeImmutable;
use Exception;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use FactorioItemBrowser\Api\Search\Entity\Config;
use FactorioItemBrowser\Api\Search\Entity\ConfigFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

/**
 * The PHPUnit test of the ConfigFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\ConfigFactory
 */
class ConfigFactoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the invoking.
     * @throws ReflectionException
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $libraryConfig = ['abc' => 'def'];
        $config = [
            ConfigKey::PROJECT => [
                ConfigKey::LIBRARY => $libraryConfig,
            ],
        ];

        /* @var Config&MockObject $configEntity */
        $configEntity = $this->createMock(Config::class);

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                  ->method('get')
                  ->with($this->identicalTo('config'))
                  ->willReturn($config);

        /* @var ConfigFactory&MockObject $factory */
        $factory = $this->getMockBuilder(ConfigFactory::class)
                        ->setMethods(['createConfigEntity'])
                        ->getMock();
        $factory->expects($this->once())
                ->method('createConfigEntity')
                ->with($this->identicalTo($libraryConfig))
                ->willReturn($configEntity);

        $result = $factory($container, Config::class);

        $this->assertSame($configEntity, $result);
    }

    /**
     * Tests the createConfigEntity method.
     * @throws Exception
     * @covers ::createConfigEntity
     */
    public function testCreateConfigEntity(): void
    {
        $config = [
            ConfigKey::MAX_SEARCH_RESULTS => 42,
            ConfigKey::MAX_CACHE_AGE => '2038-01-19',
            ConfigKey::FETCHERS => ['abc', 'def'],
            ConfigKey::SERIALIZERS => ['ghi', 'jkl'],
        ];

        $expectedResult = new Config();
        $expectedResult->setMaxSearchResults(42)
                       ->setMaxCacheAge(new DateTimeImmutable('2038-01-19'))
                       ->setFetcherAliases(['abc', 'def'])
                       ->setSerializerAliases(['ghi', 'jkl']);

        $factory = new ConfigFactory();
        $result = $this->invokeMethod($factory, 'createConfigEntity', $config);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the createConfigEntity method with throwing an exception.
     * @throws ReflectionException
     * @covers ::createConfigEntity
     */
    public function testCreateConfigEntityWithException(): void
    {
        $config = [
            ConfigKey::MAX_SEARCH_RESULTS => 42,
            ConfigKey::MAX_CACHE_AGE => 'foo',
            ConfigKey::FETCHERS => ['abc', 'def'],
            ConfigKey::SERIALIZERS => ['ghi', 'jkl'],
        ];

        $this->expectException(ServiceNotCreatedException::class);

        $factory = new ConfigFactory();
        $this->invokeMethod($factory, 'createConfigEntity', $config);
    }
}
