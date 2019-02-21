<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Filter\DataFilter;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class fetching recipes matching the query.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeFetcher implements FetcherInterface
{
    /**
     * The data filter.
     * @var DataFilter
     */
    protected $dataFilter;

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
     * Initializes the fetcher.
     * @param DataFilter $dataFilter
     * @param MapperManagerInterface $mapperManager
     * @param RecipeRepository $recipeRepository
     */
    public function __construct(
        DataFilter $dataFilter,
        MapperManagerInterface $mapperManager,
        RecipeRepository $recipeRepository
    ) {
        $this->dataFilter = $dataFilter;
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
        $recipes = $this->fetchRecipes($query);
        foreach ($this->dataFilter->filter($recipes) as $recipe) {
            if ($recipe instanceof RecipeData) {
                $searchResults->addRecipe($this->mapRecipeData($recipe));
            }
        }
    }

    /**
     * Fetches the recipes matching the query.
     * @param Query $query
     * @return array|RecipeData[]
     */
    protected function fetchRecipes(Query $query): array
    {
        return $this->recipeRepository->findDataByKeywords(
            $query->getTermValuesByType(TermType::GENERIC),
            $query->getModCombinationIds()
        );
    }

    /**
     * Maps the specified recipe data to a result.
     * @param RecipeData $recipe
     * @return RecipeResult
     * @throws MapperException
     */
    protected function mapRecipeData(RecipeData $recipe): RecipeResult
    {
        $result = new RecipeResult();
        $this->mapperManager->map($recipe, $result);

        $result->setPriority(SearchResultPriority::EXACT_MATCH);
        return $result;
    }
}