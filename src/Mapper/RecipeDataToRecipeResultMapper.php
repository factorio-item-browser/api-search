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
 *
 * @implements StaticMapperInterface<RecipeData, RecipeResult>
 */
class RecipeDataToRecipeResultMapper implements StaticMapperInterface
{
    public function getSupportedSourceClass(): string
    {
        return RecipeData::class;
    }

    public function getSupportedDestinationClass(): string
    {
        return RecipeResult::class;
    }

    /**
     * @param RecipeData $source
     * @param RecipeResult $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->setName($source->getName());
        if ($source->getMode() === RecipeMode::EXPENSIVE) {
            $destination->setExpensiveRecipeId($source->getId());
        } else {
            $destination->setNormalRecipeId($source->getId());
        }
    }
}
