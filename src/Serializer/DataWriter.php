<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Exception\WriterException;
use Ramsey\Uuid\UuidInterface;

/**
 * The class writing the serialized data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class DataWriter
{
    private string $data = '';

    /**
     * @param int $byte
     * @return $this
     * @throws WriterException
     */
    public function writeByte(int $byte): self
    {
        if ($byte < 0 || $byte > 255) {
            throw new WriterException(sprintf('Byte value %d is outside its range.', $byte));
        }

        $this->data .= pack('C', $byte);
        return $this;
    }

    /**
     * @param int $short
     * @return $this
     * @throws WriterException
     */
    public function writeShort(int $short): self
    {
        if ($short < 0 || $short > 65535) {
            throw new WriterException(sprintf('Short value %d is outside its range.', $short));
        }

        if ($short < 255) {
            $this->data .= pack('C', $short);
        } else {
            $this->data .= pack('Cn', 255, $short);
        }
        return $this;
    }

    public function writeId(UuidInterface $id): self
    {
        $this->data .= $id->getBytes();
        return $this;
    }

    public function toString(): string
    {
        return $this->data;
    }
}
