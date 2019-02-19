<?php

declare(strict_types=1);

/**
 * The configuration of the API search dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use Blast\ReflectionFactory\ReflectionFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'aliases' => [
            SearchManagerInterface::class => SearchManager::class,
        ],
        'factories'  => [
            Fetcher\FetcherManager::class => Fetcher\FetcherManagerFactory::class,
            Fetcher\ItemFetcher::class => ReflectionFactory::class,
            Fetcher\MissingItemIdFetcher::class => ReflectionFactory::class,

            Mapper\ItemToItemResultMapper::class => InvokableFactory::class,

            Parser\QueryParser::class => InvokableFactory::class,

            SearchManager::class => ReflectionFactory::class,

            Serializer\ItemResultSerializer::class => ReflectionFactory::class,
            Serializer\SerializerManager::class => Serializer\SerializerManagerFactory::class,
            Serializer\RecipeResultSerializer::class => InvokableFactory::class,

            Service\CachedSearchResultService::class => ReflectionFactory::class,
        ],
    ],
];
