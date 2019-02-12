<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Collection\RecipeCollection;

/**
 * The class representing an item result of the search.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemResult implements ResultInterface
{
    /**
     * The type of the item.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the item.
     * @var string
     */
    protected $name = '';

    /**
     * The id of the item.
     * @var int
     */
    protected $id = 0;

    /**
     * The recipes of the item.
     * @var RecipeCollection
     */
    protected $recipes;

    /**
     * The priority of the result.
     * @var int
     */
    protected $priority = SearchResultPriority::ANY_MATCH;

    /**
     * Initializes the item result.
     */
    public function __construct()
    {
        $this->recipes = new RecipeCollection();
    }

    /**
     * Sets the type of the item.
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the item.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the item.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the item.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the id of the item.
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the item.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Adds the specified recipe to the item.
     * @param RecipeResult $recipe
     * @return ItemResult
     */
    public function addRecipe(RecipeResult $recipe): self
    {
        $this->recipes->add($recipe);
        return $this;
    }

    /**
     * Returns the recipes of the item.
     * @return array|RecipeResult[]
     */
    public function getRecipes()
    {
        return $this->recipes->getAll();
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
     * Merges the specified item into the current instance.
     * @param ItemResult $item
     */
    public function merge(ItemResult $item): void
    {
        if ($item->getId() > 0) {
            $this->id = $item->getId();
        }
        foreach ($item->getRecipes() as $recipe) {
            $this->recipes->add($recipe);
        }
        $this->priority = min($this->priority, $item->getPriority());
    }
}
