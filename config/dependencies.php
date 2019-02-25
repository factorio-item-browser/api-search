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
            SearchCacheClearInterface::class => Service\CachedSearchResultService::class,
            SearchManagerInterface::class => SearchManager::class,
        ],
        'factories'  => [
            Entity\Config::class => Entity\ConfigFactory::class,

            Fetcher\DuplicateRecipeFetcher::class => InvokableFactory::class,
            Fetcher\ItemFetcher::class => ReflectionFactory::class,
            Fetcher\MissingItemIdFetcher::class => ReflectionFactory::class,
            Fetcher\MissingRecipeIdFetcher::class => ReflectionFactory::class,
            Fetcher\ProductRecipeFetcher::class => ReflectionFactory::class,
            Fetcher\RecipeFetcher::class => ReflectionFactory::class,
            Fetcher\TranslationFetcher::class => ReflectionFactory::class,

            Mapper\ItemToItemResultMapper::class => InvokableFactory::class,
            Mapper\RecipeDataToRecipeResultMapper::class => InvokableFactory::class,
            Mapper\TranslationPriorityDataToItemResultMapper::class => InvokableFactory::class,
            Mapper\TranslationPriorityDataToRecipeResultMapper::class => InvokableFactory::class,

            Parser\QueryParser::class => InvokableFactory::class,

            SearchManager::class => SearchManagerFactory::class,

            Serializer\ItemResultSerializer::class => ReflectionFactory::class,
            Serializer\RecipeResultSerializer::class => InvokableFactory::class,

            Service\CachedSearchResultService::class => Service\CachedSearchResultServiceFactory::class,
            Service\FetcherService::class => Service\FetcherServiceFactory::class,
            Service\SerializerService::class => Service\SerializerServiceFactory::class,
        ],
    ],
];
