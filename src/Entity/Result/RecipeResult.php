<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The class representing a recipe result of the search.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeResult implements ResultInterface
{
    /**
     * The name of the recipe.
     * @var string
     */
    protected $name = '';

    /**
     * The id of the normal variant of the recipe.
     * @var int
     */
    protected $normalRecipeId = 0;

    /**
     * The id of the expensive variant of the recipe.
     * @var int
     */
    protected $expensiveRecipeId = 0;

    /**
     * The priority of the result.
     * @var int
     */
    protected $priority = SearchResultPriority::ANY_MATCH;

    /**
     * Returns the type of the result.
     * @return string
     */
    public function getType(): string
    {
        return EntityType::RECIPE;
    }

    /**
     * Sets the name of the recipe.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the recipe.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the id of the normal variant of the recipe.
     * @param int $normalRecipeId
     * @return $this
     */
    public function setNormalRecipeId(int $normalRecipeId): self
    {
        $this->normalRecipeId = $normalRecipeId;
        return $this;
    }

    /**
     * Returns the id of the normal variant of the recipe.
     * @return int
     */
    public function getNormalRecipeId(): int
    {
        return $this->normalRecipeId;
    }

    /**
     * Sets the id of the expensive variant of the recipe.
     * @param int $expensiveRecipeId
     * @return $this
     */
    public function setExpensiveRecipeId(int $expensiveRecipeId): self
    {
        $this->expensiveRecipeId = $expensiveRecipeId;
        return $this;
    }

    /**
     * Returns the id of the expensive variant of the recipe.
     * @return int
     */
    public function getExpensiveRecipeId(): int
    {
        return $this->expensiveRecipeId;
    }

    /**
     * Sets the priority of the result.
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Returns the priority of the result.
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Merges the specified recipe into the current instance.
     * @param RecipeResult $recipe
     */
    public function merge(RecipeResult $recipe): void
    {
        if ($recipe->getNormalRecipeId() > 0) {
            $this->normalRecipeId = $recipe->getNormalRecipeId();
        }
        if ($recipe->getExpensiveRecipeId() > 0) {
            $this->expensiveRecipeId = $recipe->getExpensiveRecipeId();
        }
        $this->priority = min($this->priority, $recipe->getPriority());
    }
}
