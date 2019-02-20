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
 */
class TranslationPriorityDataToRecipeResultMapper implements StaticMapperInterface
{
    /**
     * Returns the source class supported by this mapper.
     * @return string
     */
    public function getSupportedSourceClass(): string
    {
        return TranslationPriorityData::class;
    }

    /**
     * Returns the destination class supported by this mapper.
     * @return string
     */
    public function getSupportedDestinationClass(): string
    {
        return RecipeResult::class;
    }

    /**
     * Maps the source object to the destination one.
     * @param TranslationPriorityData $source
     * @param RecipeResult $destination
     */
    public function map($source, $destination): void
    {
        $destination->setName($source->getName())
                    ->setPriority($source->getPriority());
    }
}
