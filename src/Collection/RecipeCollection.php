<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;

/**
 * The collection holding and merging recipes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeCollection
{
    /**
     * The recipes of the collection.
     * @var array|RecipeResult[]
     */
    protected $recipes = [];

    /**
     * Adds a recipe to the collection.
     * @param RecipeResult $recipe
     * @return RecipeCollection
     */
    public function add(RecipeResult $recipe): self
    {
        $key = $this->getKey($recipe);
        if ($key === '') {
            $this->recipes[] = $recipe;
        } elseif (isset($this->recipes[$key])) {
            $this->recipes[$key]->merge($recipe);
        } else {
            $this->recipes[$key] = $recipe;
        }
        return $this;
    }

    /**
     * Removes the recipe from the collection.
     * @param RecipeResult $recipe
     * @return RecipeCollection
     */
    public function remove(RecipeResult $recipe): self
    {
        $key = $this->getKey($recipe);
        unset($this->recipes[$key]);
        return $this;
    }

    /**
     * Returns all recipes from the collection.
     * @return array|RecipeResult[]
     */
    public function getAll(): array
    {
        sort($this->recipes);
        return array_values($this->recipes);
    }

    /**
     * Returns the key of the recipe.
     * @param RecipeResult $recipe
     * @return string
     */
    protected function getKey(RecipeResult $recipe): string
    {
        return $recipe->getName();
    }
}
