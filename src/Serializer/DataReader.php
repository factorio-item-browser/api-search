<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The class reading the serialized data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class DataReader
{
    private string $data;
    private int $length;
    private int $currentPosition = 0;

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->length = strlen($data);
    }

    /**
     * @param int $length
     * @return string
     * @throws ReaderException
     */
    private function read(int $length): string
    {
        if ($this->currentPosition + $length > $this->length) {
            throw new ReaderException('Trying to read past the end of the data.');
        }

        $result = substr($this->data, $this->currentPosition, $length);
        $this->currentPosition += $length;
        return $result;
    }

    /**
     * @return int
     * @throws ReaderException
     */
    public function readByte(): int
    {
        $data = unpack('C', $this->read(1));
        return (int) array_shift($data); // @phpstan-ignore-line
    }

    /**
     * @return int
     * @throws ReaderException
     */
    public function readShort(): int
    {
        $short = $this->readByte();
        if ($short === 255) {
            $data = unpack('n', $this->read(2));
            $short = (int) array_shift($data); // @phpstan-ignore-line
        }
        return $short;
    }

    /**
     * @return UuidInterface
     * @throws ReaderException
     */
    public function readId(): UuidInterface
    {
        return Uuid::fromBytes($this->read(16));
    }

    public function hasUnreadData(): bool
    {
        return $this->currentPosition < $this->length;
    }
}
