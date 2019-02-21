<?php

declare(strict_types=1);

/**
 * The config for the api-search library itself.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use FactorioItemBrowser\Api\Search\Constant\ConfigKey;

return [
    ConfigKey::PROJECT => [
        ConfigKey::LIBRARY => [
            ConfigKey::FETCHERS => [
                Fetcher\ItemFetcher::class,
                Fetcher\RecipeFetcher::class,
                Fetcher\TranslationFetcher::class,

                Fetcher\MissingItemIdFetcher::class,
                Fetcher\MissingRecipeIdFetcher::class,

                Fetcher\ProductRecipeFetcher::class,
                Fetcher\DuplicateRecipeFetcher::class,
            ],
            ConfigKey::SERIALIZERS => [
                Serializer\ItemResultSerializer::class,
                Serializer\RecipeResultSerializer::class,
            ],
        ],
    ],
];
