<?php

declare(strict_types=1);

/**
 * The config for the mapper manager.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use BluePsyduck\MapperManager\Constant\ConfigKey;

return [
    ConfigKey::MAIN => [
        ConfigKey::MAPPERS => [
            Mapper\ItemToItemResultMapper::class,
            Mapper\RecipeDataToRecipeResultMapper::class,
            Mapper\TranslationPriorityDataToItemResultMapper::class,
            Mapper\TranslationPriorityDataToRecipeResultMapper::class,
        ],
    ],
];
