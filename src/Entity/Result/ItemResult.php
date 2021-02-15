<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity\Result;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Search\Collection\ResultCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * The class representing an item result of the search.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemResult implements ResultInterface
{
    private string $type = '';
    private string $name = '';
    private ?UuidInterface $id = null;
    private int $priority = SearchResultPriority::ANY_MATCH;

    /** @var ResultCollection<RecipeResult> */
    private ResultCollection $recipes;

    public function __construct()
    {
        $this->recipes = new ResultCollection();
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setId(?UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function addRecipe(RecipeResult $recipe): self
    {
        $this->recipes->add($recipe);
        return $this;
    }

    /**
     * @return array<RecipeResult>
     */
    public function getRecipes(): array
    {
        return $this->recipes->getAll();
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function merge(ResultInterface $result): void
    {
        if ($result instanceof ItemResult) {
            if ($result->getId() !== null) {
                $this->id = $result->getId();
            }
            foreach ($result->getRecipes() as $recipe) {
                $this->recipes->add($recipe);
            }
            $this->priority = min($this->priority, $result->getPriority());
        }
    }
}
