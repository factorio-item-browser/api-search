<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Entity\Config;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use Interop\Container\ContainerInterface;

/**
 * The factory of the fetcher manager.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class FetcherServiceFactory
{
    /**
     * Creates the service.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return FetcherService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var Config $config */
        $config = $container->get(Config::class);

        return new FetcherService($this->createFetchers($container, $config->getFetcherAliases()));
    }

    /**
     * Creates the fetchers to use.
     * @param ContainerInterface $container
     * @param array|string[] $aliases
     * @return array|FetcherInterface[]
     */
    protected function createFetchers(ContainerInterface $container, array $aliases): array
    {
        $result = [];
        foreach ($aliases as $alias) {
            $result[] = $container->get($alias);
        }
        return $result;
    }
}
