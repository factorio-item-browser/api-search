<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The class fetching recipes where already-found items are products of.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ProductRecipeFetcher implements FetcherInterface
{
    public function __construct(
        private readonly MapperManagerInterface $mapperManager,
        private readonly RecipeRepository $recipeRepository
    ) {
    }

    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $items = [];
        $itemIds = [];
        foreach ($searchResults->getItems() as $item) {
            if ($item->getId() !== null) {
                $items[$item->getId()->toString()] = $item;
                $itemIds[] = $item->getId();
            }
        }

        $recipes = $this->recipeRepository->findDataByProductItemIds($query->getCombinationId(), $itemIds);
        foreach ($recipes as $recipe) {
            if ($recipe->getItemId() !== null) {
                $recipeItemId = $recipe->getItemId()->toString();
                if (isset($items[$recipeItemId])) {
                    $items[$recipeItemId]->addRecipe($this->mapperManager->map($recipe, new RecipeResult()));
                }
            }
        }
    }
}
