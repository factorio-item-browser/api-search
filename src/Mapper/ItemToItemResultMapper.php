<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;

/**
 * The mapper for the item results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemToItemResultMapper implements StaticMapperInterface
{
    /**
     * Returns the source class supported by this mapper.
     * @return string
     */
    public function getSupportedSourceClass(): string
    {
        return Item::class;
    }

    /**
     * Returns the destination class supported by this mapper.
     * @return string
     */
    public function getSupportedDestinationClass(): string
    {
        return ItemResult::class;
    }

    /**
     * Maps the source object to the destination one.
     * @param Item $source
     * @param ItemResult $destination
     */
    public function map($source, $destination): void
    {
        $destination->setId($source->getId())
                    ->setType($source->getType())
                    ->setName($source->getName());
    }
}
