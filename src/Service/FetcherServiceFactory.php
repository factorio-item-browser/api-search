<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Fetcher\ItemFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\MissingItemIdFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher;
use FactorioItemBrowser\Api\Search\Fetcher\TranslationFetcher;
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
     * The fetcher classes to use.
     */
    protected const FETCHER_CLASSES = [
        ItemFetcher::class,
        RecipeFetcher::class,
        TranslationFetcher::class,

        MissingItemIdFetcher::class,
        MissingRecipeIdFetcher::class,
    ];

    /**
     * Creates the service.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return FetcherService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FetcherService($this->createFetchers($container));
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
