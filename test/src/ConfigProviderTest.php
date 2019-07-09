<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search;

use BluePsyduck\MapperManager\Constant\ConfigKey as MapperManagerConfigKey;
use FactorioItemBrowser\Api\Search\ConfigProvider;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ConfigProvider class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * Tests the invoking.
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $configProvider = new ConfigProvider();
        $result = $configProvider();

        $this->assertArrayHasKey(ConfigKey::PROJECT, $result);
        $this->assertArrayHasKey(ConfigKey::API_SEARCH, $result[ConfigKey::PROJECT]);
        $this->assertArrayHasKey(ConfigKey::FETCHERS, $result[ConfigKey::PROJECT][ConfigKey::API_SEARCH]);
        $this->assertArrayHasKey(ConfigKey::SERIALIZERS, $result[ConfigKey::PROJECT][ConfigKey::API_SEARCH]);

        $this->assertArrayHasKey('dependencies', $result);
        $this->assertArrayHasKey('factories', $result['dependencies']);

        $this->assertArrayHasKey(MapperManagerConfigKey::MAIN, $result);
        $this->assertArrayHasKey(MapperManagerConfigKey::MAPPERS, $result[MapperManagerConfigKey::MAIN]);
    }
}
