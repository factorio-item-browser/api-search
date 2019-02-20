<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\RecipeMode;

/**
 * The class mapping recipe data to recipe results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeDataToRecipeResultMapper implements StaticMapperInterface
{
    /**
     * Returns the source class supported by this mapper.
     * @return string
     */
    public function getSupportedSourceClass(): string
    {
        return RecipeData::class;
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
     * @param RecipeData $source
     * @param RecipeResult $destination
     */
    public function map($source, $destination): void
    {
        $destination->setName($source->getName());
        if ($source->getMode() === RecipeMode::EXPENSIVE) {
            $destination->setExpensiveRecipeId($source->getId());
        } else {
            $destination->setNormalRecipeId($source->getId());
        }
    }
}
