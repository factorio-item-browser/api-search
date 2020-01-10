<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;

/**
 * The collection aggregating results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AggregatingResultCollection
{
    /**
     * The aggregated items.
     * @var ItemCollection
     */
    protected $items;

    /**
     * The aggregated recipes.
     * @var RecipeCollection
     */
    protected $recipes;

    /**
     * Initializes the collection.
     */
    public function __construct()
    {
        $this->items = new ItemCollection();
        $this->recipes = new RecipeCollection();
    }

    /**
     * Adds an item to the collection.
     * @param ItemResult $item
     * @return $this
     */
    public function addItem(ItemResult $item): self
    {
        $this->items->add($item);
        return $this;
    }

    /**
     * Removes the specified item from the collection.
     * @param ItemResult $item
     * @return $this
     */
    public function removeItem(ItemResult $item): self
    {
        $this->items->remove($item);
        return $this;
    }

    /**
     * Returns the items from the collection.
     * @return array|ItemResult[]
     */
    public function getItems(): array
    {
        return $this->items->getAll();
    }

    /**
     * Adds an recipe to the collection.
     * @param RecipeResult $recipe
     * @return $this
     */
    public function addRecipe(RecipeResult $recipe): self
    {
        $this->recipes->add($recipe);
        return $this;
    }

    /**
     * Removes the specified recipe from the collection.
     * @param RecipeResult $recipe
     * @return $this
     */
    public function removeRecipe(RecipeResult $recipe): self
    {
        $this->recipes->remove($recipe);
        return $this;
    }

    /**
     * Returns the recipes from the collection.
     * @return array|RecipeResult[]
     */
    public function getRecipes(): array
    {
        return $this->recipes->getAll();
    }

    /**
     * Returns the merged and sorted results of the collection.
     * @return array|ResultInterface[]
     */
    public function getMergedResults(): array
    {
        $allResults = array_merge(
            $this->items->getAll(),
            $this->recipes->getAll()
        );

        usort($allResults, [$this, 'compareResults']);

        return $allResults;
    }

    /**
     * Compares the two specified results.
     * @param ResultInterface $left
     * @param ResultInterface $right
     * @return int
     */
    protected function compareResults(ResultInterface $left, ResultInterface $right): int
    {
        $leftCriteria = $this->getSortCriteria($left);
        $rightCriteria = $this->getSortCriteria($right);

        $result = 0;
        while ($result === 0 && count($leftCriteria) > 0) {
            $result = array_shift($leftCriteria) <=> array_shift($rightCriteria);
        }
        return $result;
    }

    /**
     * Returns the sort criteria for the specified result.
     * @param ResultInterface $result
     * @return array<mixed>
     */
    protected function getSortCriteria(ResultInterface $result): array
    {
        return [
            $result->getPriority(),
            $result->getName(),
            $result->getType(),
        ];
    }
}
