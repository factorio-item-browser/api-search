<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use Ramsey\Uuid\UuidInterface;

/**
 * The class actually filtering any recipes which are duplicated in the results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class DuplicateRecipeFetcher implements FetcherInterface
{
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $itemsByRecipeIds = $this->getItemsByRecipeIds($searchResults);
        foreach ($searchResults->getRecipes() as $recipe) {
            foreach ([$recipe->getNormalRecipeId(), $recipe->getExpensiveRecipeId()] as $recipeId) {
                if ($recipeId instanceof UuidInterface) {
                    $this->filterRecipe($searchResults, $recipe, $itemsByRecipeIds[$recipeId->toString()] ?? []);
                }
            }
        }
    }

    /**
     * @param AggregatingResultCollection $searchResults
     * @return array<string, array<ItemResult>>
     */
    private function getItemsByRecipeIds(AggregatingResultCollection $searchResults): array
    {
        $items = [];
        foreach ($searchResults->getItems() as $item) {
            foreach ($item->getRecipes() as $recipe) {
                foreach ([$recipe->getNormalRecipeId(), $recipe->getExpensiveRecipeId()] as $recipeId) {
                    if ($recipeId instanceof UuidInterface) {
                        $items[$recipeId->toString()][] = $item;
                    }
                }
            }
        }
        return $items;
    }

    /**
     * @param AggregatingResultCollection $searchResults
     * @param RecipeResult $recipe
     * @param array<ItemResult> $itemsWithRecipe
     */
    private function filterRecipe(
        AggregatingResultCollection $searchResults,
        RecipeResult $recipe,
        array $itemsWithRecipe
    ): void {
        if (count($itemsWithRecipe) > 0) {
            $searchResults->removeRecipe($recipe);
            foreach ($itemsWithRecipe as $item) {
                $item->setPriority(min($item->getPriority(), $recipe->getPriority()));
            }
        }
    }
}
