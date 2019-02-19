<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use Interop\Container\ContainerInterface;

/**
 * The factory of the fetcher manager.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class FetcherManagerFactory
{
    /**
     * The fetcher classes to use.
     */
    protected const FETCHER_CLASSES = [
        ItemFetcher::class,
        MissingItemIdFetcher::class,
    ];

    /**
     * Creates the fetcher manager.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return FetcherManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FetcherManager($this->createFetchers($container));
    }

    /**
     * Creates the fetchers to use.
     * @param ContainerInterface $container
     * @return array|FetcherInterface[]
     */
    protected function createFetchers(ContainerInterface $container): array
    {
        $result = [];
        foreach (self::FETCHER_CLASSES as $alias) {
            $result[] = $container->get($alias);
        }
        return $result;
    }
}
