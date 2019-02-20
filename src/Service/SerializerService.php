<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;

/**
 * The service handling the serializers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SerializerService
{
    /**
     * The serializers by their handled result class.
     * @var array|SerializerInterface[]
     */
    protected $serializersByClassName = [];

    /**
     * The serializers by their serialized type.
     * @var array|SerializerInterface[]
     */
    protected $serializersByType = [];

    /**
     * Initializes the service.
     * @param array|SerializerInterface[] $serializers
     */
    public function __construct(array $serializers)
    {
        foreach ($serializers as $serializer) {
            $this->serializersByClassName[$serializer->getHandledResultClass()] = $serializer;
            $this->serializersByType[$serializer->getSerializedType()] = $serializer;
        }
    }

    /**
     * Serializes the collection of search results.
     * @param PaginatedResultCollection $searchResults
     * @return string
     */
    public function serialize(PaginatedResultCollection $searchResults): string
    {
        $results = [];
        foreach ($searchResults->getResults(0, $searchResults->count()) as $searchResult) {
            $results[] = $this->serializeResult($searchResult);
        }

        return implode('|', array_filter($results));
    }

    /**
     * Serializes the result.
     * @param ResultInterface $searchResult
     * @return string
     */
    protected function serializeResult(ResultInterface $searchResult): string
    {
        $result = '';
        $className = get_class($searchResult);
        if (isset($this->serializersByClassName[$className])) {
            $serializer = $this->serializersByClassName[$className];
            $result = $serializer->getSerializedType() . $serializer->serialize($searchResult);
        }
        return $result;
    }

    /**
     * Unserializes the serialized result to a collection.
     * @param string $serializedResults
     * @return PaginatedResultCollection
     */
    public function unserialize(string $serializedResults): PaginatedResultCollection
    {
        $result = $this->createResultCollection();
        foreach (explode('|', $serializedResults) as $serializedResult) {
            $searchResult = $this->unserializeResult($serializedResult);
            if ($searchResult instanceof ResultInterface) {
                $result->add($searchResult);
            }
        }
        return $result;
    }

    /**
     * Creates a new result collection.
     * @return PaginatedResultCollection
     */
    protected function createResultCollection(): PaginatedResultCollection
    {
        return new PaginatedResultCollection();
    }

    /**
     * Unserializes the serialized result to an entity, if possible.
     * @param string $serializedResult
     * @return ResultInterface|null
     */
    protected function unserializeResult(string $serializedResult): ?ResultInterface
    {
        $result = null;
        $type = substr($serializedResult, 0, 1);
        if (isset($this->serializersByType[$type])) {
            $result = $this->serializersByType[$type]->unserialize(substr($serializedResult, 1));
        }
        return $result;
    }
}
