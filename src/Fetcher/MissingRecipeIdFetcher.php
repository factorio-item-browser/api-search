<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class fetching missing ids of already matched recipes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MissingRecipeIdFetcher implements FetcherInterface
{
    private MapperManagerInterface $mapperManager;
    private RecipeRepository $recipeRepository;

    public function __construct(
        MapperManagerInterface $mapperManager,
        RecipeRepository $recipeRepository
    ) {
        $this->mapperManager = $mapperManager;
        $this->recipeRepository = $recipeRepository;
    }

    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $recipeNames = $this->getRecipeNamesWithMissingId($searchResults);
        $recipes = $this->recipeRepository->findDataByNames($query->getCombinationId(), $recipeNames);
        foreach ($recipes as $recipe) {
            $searchResults->addRecipe($this->mapperManager->map($recipe, new RecipeResult()));
        }
    }

    /**
     * @param AggregatingResultCollection $searchResults
     * @return array<string>
     */
    private function getRecipeNamesWithMissingId(AggregatingResultCollection $searchResults): array
    {
        $recipeNames = [];
        foreach ($searchResults->getRecipes() as $recipe) {
            if ($recipe->getNormalRecipeId() === null && $recipe->getExpensiveRecipeId() === null) {
                $recipeNames[] = $recipe->getName();
            }
        }
        return $recipeNames;
    }
}
