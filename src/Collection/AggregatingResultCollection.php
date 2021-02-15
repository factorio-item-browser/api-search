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
    /** @var ResultCollection<ItemResult> */
    private ResultCollection $items;
    /** @var ResultCollection<RecipeResult>  */
    private ResultCollection $recipes;

    public function __construct()
    {
        $this->items = new ResultCollection();
        $this->recipes = new ResultCollection();
    }

    public function addItem(ItemResult $item): self
    {
        $this->items->add($item);
        return $this;
    }

    public function removeItem(ItemResult $item): self
    {
        $this->items->remove($item);
        return $this;
    }

    /**
     * @return array<ItemResult>
     */
    public function getItems(): array
    {
        return $this->items->getAll();
    }

    public function addRecipe(RecipeResult $recipe): self
    {
        $this->recipes->add($recipe);
        return $this;
    }

    public function removeRecipe(RecipeResult $recipe): self
    {
        $this->recipes->remove($recipe);
        return $this;
    }

    /**
     * @return array<RecipeResult>
     */
    public function getRecipes(): array
    {
        return $this->recipes->getAll();
    }

    /**
     * @return array<ResultInterface>
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

    private function compareResults(ResultInterface $left, ResultInterface $right): int
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
     * @param ResultInterface $result
     * @return array<mixed>
     */
    private function getSortCriteria(ResultInterface $result): array
    {
        return [
            $result->getPriority(),
            $result->getName(),
            $result->getType(),
        ];
    }
}
