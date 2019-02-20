<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Mapper;

use BluePsyduck\MapperManager\Mapper\StaticMapperInterface;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;

/**
 * The class mapping translation priority data to an item result.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationPriorityDataToItemResultMapper implements StaticMapperInterface
{
    /**
     * Returns the source class supported by this mapper.
     * @return string
     */
    public function getSupportedSourceClass(): string
    {
        return TranslationPriorityData::class;
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
     * @param TranslationPriorityData $source
     * @param ItemResult $destination
     */
    public function map($source, $destination): void
    {
        $destination->setType($source->getType())
                    ->setName($source->getName())
                    ->setPriority($source->getPriority());
    }
}
