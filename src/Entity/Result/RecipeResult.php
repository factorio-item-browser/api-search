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
    private string $name = '';
    private ?UuidInterface $normalRecipeId = null;
    private ?UuidInterface $expensiveRecipeId = null;
    private int $priority = SearchResultPriority::ANY_MATCH;

    public function getType(): string
    {
        return EntityType::RECIPE;
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

    public function setNormalRecipeId(?UuidInterface $normalRecipeId): self
    {
        $this->normalRecipeId = $normalRecipeId;
        return $this;
    }

    public function getNormalRecipeId(): ?UuidInterface
    {
        return $this->normalRecipeId;
    }

    public function setExpensiveRecipeId(?UuidInterface $expensiveRecipeId): self
    {
        $this->expensiveRecipeId = $expensiveRecipeId;
        return $this;
    }

    public function getExpensiveRecipeId(): ?UuidInterface
    {
        return $this->expensiveRecipeId;
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
        if ($result instanceof RecipeResult) {
            if ($result->getNormalRecipeId() !== null) {
                $this->normalRecipeId = $result->getNormalRecipeId();
            }
            if ($result->getExpensiveRecipeId() !== null) {
                $this->expensiveRecipeId = $result->getExpensiveRecipeId();
            }
            $this->priority = min($this->priority, $result->getPriority());
        }
    }
}
