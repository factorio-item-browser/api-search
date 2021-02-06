<?php

declare(strict_types=1);

/**
 * The configuration of the API search dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use BluePsyduck\LaminasAutoWireFactory\AutoWireFactory;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;

use function BluePsyduck\LaminasAutoWireFactory\injectAliasArray;
use function BluePsyduck\LaminasAutoWireFactory\readConfig;

return [
    'dependencies' => [
        'aliases' => [
            SearchCacheClearInterface::class => Service\CachedSearchResultService::class,
            SearchManagerInterface::class => SearchManager::class,
        ],
        'factories'  => [
            Fetcher\DuplicateRecipeFetcher::class => AutoWireFactory::class,
            Fetcher\ItemFetcher::class => AutoWireFactory::class,
            Fetcher\MissingItemIdFetcher::class => AutoWireFactory::class,
            Fetcher\MissingRecipeIdFetcher::class => AutoWireFactory::class,
            Fetcher\ProductRecipeFetcher::class => AutoWireFactory::class,
            Fetcher\RecipeFetcher::class => AutoWireFactory::class,
            Fetcher\TranslationFetcher::class => AutoWireFactory::class,

            Mapper\ItemToItemResultMapper::class => AutoWireFactory::class,
            Mapper\RecipeDataToRecipeResultMapper::class => AutoWireFactory::class,
            Mapper\TranslationPriorityDataToItemResultMapper::class => AutoWireFactory::class,
            Mapper\TranslationPriorityDataToRecipeResultMapper::class => AutoWireFactory::class,

            Parser\QueryParser::class => AutoWireFactory::class,

            SearchManager::class => AutoWireFactory::class,

            Serializer\ItemResultSerializer::class => AutoWireFactory::class,
            Serializer\RecipeResultSerializer::class => AutoWireFactory::class,

            Service\CachedSearchResultService::class => AutoWireFactory::class,
            Service\FetcherService::class => AutoWireFactory::class,
            Service\SerializerService::class => AutoWireFactory::class,

            // Auto-wire helpers
            'array $apiSearchFetchers' => injectAliasArray(ConfigKey::MAIN, ConfigKey::FETCHERS),
            'array $apiSearchSerializers' => injectAliasArray(ConfigKey::MAIN, ConfigKey::SERIALIZERS),

            'int $apiSearchMaxSearchResults' => readConfig(ConfigKey::MAIN, ConfigKey::MAX_SEARCH_RESULTS),

            'string $apiSearchMaxCacheAge' => readConfig(ConfigKey::MAIN, ConfigKey::MAX_CACHE_AGE),
        ],
    ],
];
