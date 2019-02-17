<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;

/**
 * The interface of the result serializers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SerializerInterface
{
    /**
     * Returns the class this serializer is actually handling.
     * @return string
     */
    public function getHandledResultClass(): string;

    /**
     * Returns the serialized type.
     * @return string
     */
    public function getSerializedType(): string;

    /**
     * Serializes the specified result into a string.
     * @param ResultInterface $result
     * @return string
     */
    public function serialize($result): string;

    /**
     * Unserializes the result into an entity.
     * @param string $serializedResult
     * @return ResultInterface
     */
    public function unserialize(string $serializedResult): ResultInterface;
}
