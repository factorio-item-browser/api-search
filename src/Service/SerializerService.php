<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use BluePsyduck\LaminasAutoWireFactory\Attribute\InjectAliasArray;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Exception\WriterException;
use FactorioItemBrowser\Api\Search\Serializer\DataReader;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;
use FactorioItemBrowser\Api\Search\Serializer\DataWriter;

/**
 * The service handling the serializers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SerializerService
{
    /** @var array<string, SerializerInterface<ResultInterface>> */
    private array $serializersByClassName = [];
    /** @var array<int, SerializerInterface<ResultInterface>> */
    private array $serializersByType = [];

    /**
     * @param array<SerializerInterface<ResultInterface>> $serializers
     */
    public function __construct(
        #[InjectAliasArray(ConfigKey::MAIN, ConfigKey::SERIALIZERS)]
        array $serializers
    ) {
        foreach ($serializers as $serializer) {
            $this->serializersByClassName[$serializer->getHandledResultClass()] = $serializer;
            $this->serializersByType[$serializer->getSerializedType()] = $serializer;
        }
    }

    /**
     * @param PaginatedResultCollection $searchResults
     * @return string
     * @throws WriterException
     */
    public function serialize(PaginatedResultCollection $searchResults): string
    {
        $writer = new DataWriter();
        foreach ($searchResults->getResults(0, $searchResults->count()) as $searchResult) {
            $className = get_class($searchResult);
            $serializer = $this->serializersByClassName[$className] ?? null;
            if ($serializer === null) {
                throw new WriterException(sprintf('Unable to serialize class %s.', $className));
            }
            $writer->writeByte($serializer->getSerializedType());
            $serializer->serialize($writer, $searchResult);
        }
        return $writer->toString();
    }

    /**
     * @param string $serializedResults
     * @return PaginatedResultCollection
     * @throws ReaderException
     */
    public function unserialize(string $serializedResults): PaginatedResultCollection
    {
        $reader = new DataReader($serializedResults);
        $paginatedResults = new PaginatedResultCollection();
        while ($reader->hasUnreadData()) {
            $type = $reader->readByte();
            $serializer = $this->serializersByType[$type] ?? null;
            if ($serializer === null) {
                throw new ReaderException(sprintf('Unknown serializer type 0x%02x', $type));
            }
            $searchResult = $serializer->unserialize($reader);
            $paginatedResults->add($searchResult);
        }
        return $paginatedResults;
    }
}
