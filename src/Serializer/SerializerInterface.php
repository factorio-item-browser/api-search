<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;

/**
 * The interface of the result serializers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TResult of ResultInterface
 */
interface SerializerInterface
{
    /**
     * Returns the class this serializer is actually handling.
     * @return class-string<TResult>
     */
    public function getHandledResultClass(): string;

    /**
     * Returns the serialized type.
     * @return int
     */
    public function getSerializedType(): int;

    /**
     * Serializes the specified result into a string.
     * @param DataWriter $writer
     * @param TResult $result
     * @throws WriterException
     */
    public function serialize(DataWriter $writer, ResultInterface $result): void;

    /**
     * Unserializes the result into an entity.
     * @param DataReader $reader
     * @return TResult
     * @throws ReaderException
     */
    public function unserialize(DataReader $reader): ResultInterface;
}
