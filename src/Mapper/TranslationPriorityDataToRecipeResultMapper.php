<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class mapping translation priority data to an recipe result.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements StaticMapperInterface<TranslationPriorityData, RecipeResult>
 */
class TranslationPriorityDataToRecipeResultMapper implements StaticMapperInterface
{
    public function getSupportedSourceClass(): string
    {
        return TranslationPriorityData::class;
    }

    public function getSupportedDestinationClass(): string
    {
        return RecipeResult::class;
    }

    /**
     * @param TranslationPriorityData $source
     * @param RecipeResult $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->setName($source->getName())
                    ->setPriority($source->getPriority());
    }
}
