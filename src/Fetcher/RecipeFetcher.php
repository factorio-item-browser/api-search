<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
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
        $recipes = $this->recipeRepository->findDataByKeywords(
            $query->getCombinationId(),
            $query->getTerms()->getValuesByTypes([TermType::GENERIC]),
        );

        foreach ($recipes as $recipe) {
            $recipeResult = $this->mapperManager->map($recipe, new RecipeResult());
            $recipeResult->setPriority(SearchResultPriority::EXACT_MATCH);

            $searchResults->addRecipe($recipeResult);
        }
    }
}
