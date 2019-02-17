<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;

/**
 * The serializer for the item results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemResultSerializer implements SerializerInterface
{
    /**
     * The recipe result serializer.
     * @var RecipeResultSerializer
     */
    protected $recipeResultSerializer;

    /**
     * Initializes the serializer.
     * @param RecipeResultSerializer $recipeResultSerializer
     */
    public function __construct(RecipeResultSerializer $recipeResultSerializer)
    {
        $this->recipeResultSerializer = $recipeResultSerializer;
    }

    /**
     * Returns the class this serializer is actually handling.
     * @return string
     */
    public function getHandledResultClass(): string
    {
        return ItemResult::class;
    }

    /**
     * Returns the serialized type.
     * @return string
     */
    public function getSerializedType(): string
    {
        return SerializedResultType::ITEM;
    }

    /**
     * Serializes the specified result into a string.
     * @param ItemResult $item
     * @return string
     */
    public function serialize($item): string
    {
        return implode(',', array_merge(
            [$item->getId()],
            array_filter($this->serializeRecipes($item->getRecipes()))
        ));
    }

    /**
     * Serializes the specified recipes.
     * @param array|RecipeResult[] $recipes
     * @return array|string[]
     */
    protected function serializeRecipes(array $recipes): array
    {
        $result = [];
        foreach ($recipes as $recipe) {
            $result[] = $this->recipeResultSerializer->serialize($recipe);
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
        $ids = explode(',', $serializedResult);
        $itemId = array_shift($ids);

        $result = new ItemResult();
        $result->setId((int) $itemId);

        $this->unserializeRecipes($ids, $result);
        return $result;
    }

    /**
     * Unserializes the recipes into the item.
     * @param array|string[] $serializedRecipes
     * @param ItemResult $item
     */
    protected function unserializeRecipes(array $serializedRecipes, ItemResult $item): void
    {
        foreach ($serializedRecipes as $serializedRecipe) {
            $recipe = $this->recipeResultSerializer->unserialize($serializedRecipe);
            if ($recipe instanceof RecipeResult) {
                $item->addRecipe($recipe);
            }
        }
    }
}
