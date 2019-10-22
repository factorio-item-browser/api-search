<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use Ramsey\Uuid\Uuid;

/**
 * The serializer for the recipe results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeResultSerializer implements SerializerInterface
{
    /**
     * Returns the class this serializer is actually handling.
     * @return string
     */
    public function getHandledResultClass(): string
    {
        return RecipeResult::class;
    }

    /**
     * Returns the serialized type.
     * @return string
     */
    public function getSerializedType(): string
    {
        return SerializedResultType::RECIPE;
    }

    /**
     * Serializes the specified result into a string.
     * @param RecipeResult $recipe
     * @return string
     */
    public function serialize($recipe): string
    {
        $result = '';
        if ($recipe->getNormalRecipeId() !== null) {
            $result .= $recipe->getNormalRecipeId()->toString();
        }
        if ($recipe->getExpensiveRecipeId() !== null) {
            $result .= '+' . $recipe->getExpensiveRecipeId()->toString();
        }
        return $result;
    }

    /**
     * Unserializes the result into an entity.
     * @param string $serializedResult
     * @return ResultInterface
     */
    public function unserialize(string $serializedResult): ResultInterface
    {
        $recipeIds = explode('+', $serializedResult);

        $result = new RecipeResult();
        if (isset($recipeIds[0])) {
            $result->setNormalRecipeId(Uuid::fromString($recipeIds[0]));
        }
        if (isset($recipeIds[1])) {
            $result->setExpensiveRecipeId(Uuid::fromString($recipeIds[1]));
        }
        return $result;
    }
}
