<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Constant\SerializedResultType;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;

/**
 * The serializer for the recipe results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements SerializerInterface<RecipeResult>
 */
class RecipeResultSerializer implements SerializerInterface
{
    private const TYPE_HYBRID = 0x01;
    private const TYPE_NORMAL = 0x02;
    private const TYPE_EXPENSIVE = 0x03;

    public function getHandledResultClass(): string
    {
        return RecipeResult::class;
    }

    public function getSerializedType(): int
    {
        return SerializedResultType::RECIPE;
    }

    /**
     * @param DataWriter $writer
     * @param RecipeResult $result
     * @throws WriterException
     */
    public function serialize(DataWriter $writer, ResultInterface $result): void
    {
        $normalRecipeId = $result->getNormalRecipeId();
        $expensiveRecipeId = $result->getExpensiveRecipeId();

        if ($normalRecipeId !== null && $expensiveRecipeId !== null) {
            $writer->writeByte(self::TYPE_HYBRID)
                   ->writeId($normalRecipeId)
                   ->writeId($expensiveRecipeId);
        } elseif ($normalRecipeId !== null) {
            $writer->writeByte(self::TYPE_NORMAL)
                   ->writeId($normalRecipeId);
        } elseif ($expensiveRecipeId !== null) {
            $writer->writeByte(self::TYPE_EXPENSIVE)
                   ->writeId($expensiveRecipeId);
        } else {
            throw new WriterException('Trying to write recipe with none of its ids set.');
        }
    }

    /**
     * @param DataReader $reader
     * @return RecipeResult
     * @throws ReaderException
     */
    public function unserialize(DataReader $reader): ResultInterface
    {
        $result = new RecipeResult();
        $type = $reader->readByte();
        switch ($type) {
            case self::TYPE_HYBRID:
                $result->setNormalRecipeId($reader->readId())
                       ->setExpensiveRecipeId($reader->readId());
                break;
            case self::TYPE_NORMAL:
                $result->setNormalRecipeId($reader->readId());
                break;
            case self::TYPE_EXPENSIVE:
                $result->setExpensiveRecipeId($reader->readId());
                break;
            default:
                throw new ReaderException(sprintf('Unknown recipe type: 0x%02x', $type));
        }
        return $result;
    }
}
