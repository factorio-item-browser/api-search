<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;

/**
 * The serializer for the item results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements SerializerInterface<ItemResult>
 */
class ItemResultSerializer implements SerializerInterface
{
    private RecipeResultSerializer $recipeResultSerializer;

    public function __construct(RecipeResultSerializer $recipeResultSerializer)
    {
        $this->recipeResultSerializer = $recipeResultSerializer;
    }

    public function getHandledResultClass(): string
    {
        return ItemResult::class;
    }

    public function getSerializedType(): int
    {
        return SerializedResultType::ITEM;
    }

    /**
     * @param DataWriter $writer
     * @param ItemResult $result
     * @throws WriterException
     */
    public function serialize(DataWriter $writer, ResultInterface $result): void
    {
        $itemId = $result->getId();
        if ($itemId === null) {
            throw new WriterException('Trying to serialize an item without id');
        }

        $recipes = $result->getRecipes();
        $writer->writeId($itemId)
               ->writeShort(count($recipes));

        foreach ($recipes as $recipe) {
            $this->recipeResultSerializer->serialize($writer, $recipe);
        }
    }

    /**
     * @param DataReader $reader
     * @return ItemResult
     * @throws ReaderException
     */
    public function unserialize(DataReader $reader): ResultInterface
    {
        $result = new ItemResult();
        $result->setId($reader->readId());

        $count = $reader->readShort();
        for ($i = 0; $i < $count; ++$i) {
            $result->addRecipe($this->recipeResultSerializer->unserialize($reader));
        }

        return $result;
    }
}
