<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

/**
 * The config provider of the API search library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ConfigProvider
{
    /**
     * Returns the configuration of the library.
     * @return array
     */
    public function __invoke(): array
    {
        return array_merge(
            require(__DIR__ . '/../config/dependencies.php')
        );
    }
}
