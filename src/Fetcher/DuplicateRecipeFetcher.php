<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class actually filtering any recipes which are duplicated in the results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class DuplicateRecipeFetcher implements FetcherInterface
{
    /**
     * Fetches the data matching the specified query.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $itemsByRecipeIds = $this->getItemsByRecipeIds($searchResults);
        foreach ($searchResults->getRecipes() as $recipe) {
            if ($recipe->getNormalRecipeId() !== null) {
                $this->filterRecipe(
                    $recipe,
                    $itemsByRecipeIds[$recipe->getNormalRecipeId()->toString()] ?? [],
                    $searchResults
                );
            }
            if ($recipe->getExpensiveRecipeId() !== null) {
                $this->filterRecipe(
                    $recipe,
                    $itemsByRecipeIds[$recipe->getExpensiveRecipeId()->toString()] ?? [],
                    $searchResults
                );
            }
        }
    }

    /**
     * Returns the items by their recipe ids.
     * @param AggregatingResultCollection $searchResults
     * @return array|ItemResult[][]
     */
    protected function getItemsByRecipeIds(AggregatingResultCollection $searchResults): array
    {
        $result = [];
        foreach ($searchResults->getItems() as $item) {
            foreach ($item->getRecipes() as $recipe) {
                if ($recipe->getNormalRecipeId() !== null) {
                    $result[$recipe->getNormalRecipeId()->toString()][] = $item;
                }
                if ($recipe->getExpensiveRecipeId() !== null) {
                    $result[$recipe->getExpensiveRecipeId()->toString()][] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * Filters the recipe if there is a valid item.
     * @param RecipeResult $recipe
     * @param array|ItemResult[] $items
     * @param AggregatingResultCollection $searchResults
     */
    protected function filterRecipe(
        RecipeResult $recipe,
        array $items,
        AggregatingResultCollection $searchResults
    ): void {
        if (count($items) > 0) {
            $searchResults->removeRecipe($recipe);
            foreach ($items as $item) {
                $item->setPriority(min($item->getPriority(), $recipe->getPriority()));
            }
        }
    }
}
