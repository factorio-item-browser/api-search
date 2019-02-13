<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search;

use FactorioItemBrowser\Api\Search\ConfigProvider;
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

        $this->assertArrayHasKey('dependencies', $result);
        $this->assertArrayHasKey('factories', $result['dependencies']);
    }
}
