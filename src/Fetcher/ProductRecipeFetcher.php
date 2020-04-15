<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class fetching recipes where already-found items are products of.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ProductRecipeFetcher implements FetcherInterface
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
        $items = $this->getItems($searchResults);
        foreach ($this->fetchProductRecipes($items, $query) as $recipe) {
            $this->matchRecipeToItems($recipe, $items);
        }
    }

    /**
     * Returns the items mapped to their its.
     * @param AggregatingResultCollection $searchResults
     * @return array|ItemResult[]
     */
    protected function getItems(AggregatingResultCollection $searchResults): array
    {
        $result = [];
        foreach ($searchResults->getItems() as $item) {
            if ($item->getId() !== null) {
                $result[$item->getId()->toString()] = $item;
            }
        }
        return $result;
    }

    /**
     * Returns the recipes having any of the items as product.
     * @param array|ItemResult[] $items
     * @param Query $query
     * @return array|RecipeData[]
     */
    protected function fetchProductRecipes(array $items, Query $query): array
    {
        $itemIds = [];
        foreach ($items as $item) {
            if ($item->getId() !== null) {
                $itemIds[] = $item->getId();
            }
        }

        return $this->recipeRepository->findDataByProductItemIds($query->getCombinationId(), $itemIds);
    }

    /**
     * Matches the recipe to the items.
     * @param RecipeData $recipe
     * @param array|ItemResult[] $items
     * @throws MapperException
     */
    protected function matchRecipeToItems(RecipeData $recipe, array $items): void
    {
        if ($recipe->getItemId() === null) {
            return;
        }

        $recipeItemId = $recipe->getItemId()->toString();
        if (isset($items[$recipeItemId])) {
            $items[$recipeItemId]->addRecipe($this->mapRecipe($recipe));
        }
    }

    /**
     * Maps the recipe to a result entity.
     * @param RecipeData $recipe
     * @return RecipeResult
     * @throws MapperException
     */
    protected function mapRecipe(RecipeData $recipe): RecipeResult
    {
        $result = new RecipeResult();
        $this->mapperManager->map($recipe, $result);
        return $result;
    }
}
