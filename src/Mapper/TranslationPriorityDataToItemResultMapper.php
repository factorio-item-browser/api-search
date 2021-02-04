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
 *
 * @implements StaticMapperInterface<TranslationPriorityData, ItemResult>
 */
class TranslationPriorityDataToItemResultMapper implements StaticMapperInterface
{
    public function getSupportedSourceClass(): string
    {
        return TranslationPriorityData::class;
    }

    public function getSupportedDestinationClass(): string
    {
        return ItemResult::class;
    }

    /**
     * @param TranslationPriorityData $source
     * @param ItemResult $destination
     */
    public function map(object $source, object $destination): void
    {
        $destination->setType($source->getType())
                    ->setName($source->getName())
                    ->setPriority($source->getPriority());
    }
}
