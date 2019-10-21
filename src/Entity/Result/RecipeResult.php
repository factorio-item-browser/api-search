<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Common\Constant\EntityType;
use Ramsey\Uuid\UuidInterface;

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
     * @var UuidInterface|null
     */
    protected $normalRecipeId;

    /**
     * The id of the expensive variant of the recipe.
     * @var UuidInterface|null
     */
    protected $expensiveRecipeId;

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
     * @param UuidInterface|null $normalRecipeId
     * @return $this
     */
    public function setNormalRecipeId(?UuidInterface $normalRecipeId): self
    {
        $this->normalRecipeId = $normalRecipeId;
        return $this;
    }

    /**
     * Returns the id of the normal variant of the recipe.
     * @return UuidInterface|null
     */
    public function getNormalRecipeId(): ?UuidInterface
    {
        return $this->normalRecipeId;
    }

    /**
     * Sets the id of the expensive variant of the recipe.
     * @param UuidInterface|null $expensiveRecipeId
     * @return $this
     */
    public function setExpensiveRecipeId(?UuidInterface $expensiveRecipeId): self
    {
        $this->expensiveRecipeId = $expensiveRecipeId;
        return $this;
    }

    /**
     * Returns the id of the expensive variant of the recipe.
     * @return UuidInterface|null
     */
    public function getExpensiveRecipeId(): ?UuidInterface
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
        if ($recipe->getNormalRecipeId() !== null) {
            $this->normalRecipeId = $recipe->getNormalRecipeId();
        }
        if ($recipe->getExpensiveRecipeId() !== null) {
            $this->expensiveRecipeId = $recipe->getExpensiveRecipeId();
        }
        $this->priority = min($this->priority, $recipe->getPriority());
    }
}
