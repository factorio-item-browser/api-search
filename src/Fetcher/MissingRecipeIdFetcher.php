<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
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
    /**
     * The mapper manager.
     * @var MapperManagerInterface
     */
    protected $mapperManager;

    /**
     * The recipe repository.
     * @var RecipeRepository
     */
    protected $recipeRepository;

    /**
     * Initializes the data fetcher.
     * @param MapperManagerInterface $mapperManager
     * @param RecipeRepository $recipeRepository
     */
    public function __construct(
        MapperManagerInterface $mapperManager,
        RecipeRepository $recipeRepository
    ) {
        $this->mapperManager = $mapperManager;
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * Fetches the data matching the specified query.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     * @throws MapperException
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $recipeNames = $this->getRecipeNamesWithMissingIds($searchResults);
        foreach ($this->fetchRecipes($recipeNames, $query) as $recipe) {
            $searchResults->addRecipe($this->mapRecipeData($recipe));
        }
    }

    /**
     * Returns the names of the recipes which are still missing their ids.
     * @param AggregatingResultCollection $searchResults
     * @return array|string[]
     */
    protected function getRecipeNamesWithMissingIds(AggregatingResultCollection $searchResults): array
    {
        $recipeNames = [];
        foreach ($searchResults->getRecipes() as $recipe) {
            if ($recipe->getNormalRecipeId() === null && $recipe->getExpensiveRecipeId() === null) {
                $recipeNames[] = $recipe->getName();
            }
        }
        return $recipeNames;
    }

    /**
     * Fetches the recipes matching the criteria.
     * @param array|string[] $names
     * @param Query $query
     * @return array|RecipeData[]
     */
    protected function fetchRecipes(array $names, Query $query): array
    {
        return $this->recipeRepository->findDataByNames(
            $query->getCombinationId(),
            $names
        );
    }

    /**
     * Maps the specified recipe to a result.
     * @param RecipeData $recipe
     * @return RecipeResult
     * @throws MapperException
     */
    protected function mapRecipeData(RecipeData $recipe): RecipeResult
    {
        $result = new RecipeResult();
        $this->mapperManager->map($recipe, $result);
        return $result;
    }
}
