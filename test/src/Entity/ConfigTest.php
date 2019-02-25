<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use DateTime;
use Exception;
use FactorioItemBrowser\Api\Search\Entity\Config;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Config class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\Config
 */
class ConfigTest extends TestCase
{
    /**
     * Tests the constructing.
     * @throws Exception
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $config = new Config();

        $this->assertSame(0, $config->getMaxSearchResults());
        $this->assertSame([], $config->getFetcherAliases());
        $this->assertSame([], $config->getSerializerAliases());

        $config->getMaxCacheAge();
    }

    /**
     * Tests the setting and getting the max search results.
     * @throws Exception
     * @covers ::getMaxSearchResults
     * @covers ::setMaxSearchResults
     */
    public function testSetAndGetMaxSearchResults(): void
    {
        $maxSearchResults = 42;
        $config = new Config();

        $this->assertSame($config, $config->setMaxSearchResults($maxSearchResults));
        $this->assertSame($maxSearchResults, $config->getMaxSearchResults());
    }

    /**
     * Tests the setting and getting the max cache age.
     * @throws Exception
     * @covers ::getMaxCacheAge
     * @covers ::setMaxCacheAge
     */
    public function testSetAndGetMaxCacheAge(): void
    {
        $maxCacheAge = new DateTime('2038-01-19T03:14:07');
        $config = new Config();

        $this->assertSame($config, $config->setMaxCacheAge($maxCacheAge));
        $this->assertSame($maxCacheAge, $config->getMaxCacheAge());
    }

    /**
     * Tests the setting and getting the fetcher aliases.
     * @throws Exception
     * @covers ::getFetcherAliases
     * @covers ::setFetcherAliases
     */
    public function testSetAndGetFetcherAliases(): void
    {
        $fetcherAliases = ['abc', 'def'];
        $config = new Config();

        $this->assertSame($config, $config->setFetcherAliases($fetcherAliases));
        $this->assertSame($fetcherAliases, $config->getFetcherAliases());
    }

    /**
     * Tests the setting and getting the serializer aliases.
     * @throws Exception
     * @covers ::getSerializerAliases
     * @covers ::setSerializerAliases
     */
    public function testSetAndGetSerializerAliases(): void
    {
        $serializerAliases = ['abc', 'def'];
        $config = new Config();

        $this->assertSame($config, $config->setSerializerAliases($serializerAliases));
        $this->assertSame($serializerAliases, $config->getSerializerAliases());
    }
}
