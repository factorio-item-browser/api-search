<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;

/**
 * The collection holding and merging items.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemCollection
{
    /**
     * The items of the collection.
     * @var array|ItemResult[]
     */
    protected $items = [];

    /**
     * Adds a item to the collection.
     * @param ItemResult $item
     * @return ItemCollection
     */
    public function add(ItemResult $item): self
    {
        $key = $this->getKey($item);
        if ($key === '') {
            $this->items[] = $item;
        } elseif (isset($this->items[$key])) {
            $this->items[$key]->merge($item);
        } else {
            $this->items[$key] = $item;
        }
        return $this;
    }

    /**
     * Removes the item from the collection.
     * @param ItemResult $item
     * @return ItemCollection
     */
    public function remove(ItemResult $item): self
    {
        $key = $this->getKey($item);
        unset($this->items[$key]);
        return $this;
    }

    /**
     * Returns all items from the collection.
     * @return array|ItemResult[]
     */
    public function getAll(): array
    {
        return array_values($this->items);
    }

    /**
     * Returns the key of the item.
     * @param ItemResult $item
     * @return string
     */
    protected function getKey(ItemResult $item): string
    {
        return trim(implode('|', [$item->getType(), $item->getName()]), '|');
    }
}
