<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Exception\WriterException;
use FactorioItemBrowser\Api\Search\Serializer\DataWriter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the DataWriter class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Serializer\DataWriter
 */
class DataWriterTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideWriteByte(): array
    {
        return [
            [42, false, "\x2A"],
            [1, false, "\x01"],
            [0, false, "\x00"],
            [255, false, "\xFF"],
            [256, true, ''],
            [-42, true, ''],
        ];
    }

    /**
     * @param int $byte
     * @param bool $expectException
     * @param string $expectedData
     * @throws WriterException
     * @dataProvider provideWriteByte
     */
    public function testWriteByte(int $byte, bool $expectException, string $expectedData): void
    {
        if ($expectException) {
            $this->expectException(WriterException::class);
        }

        $instance = new DataWriter();
        $this->assertSame($instance, $instance->writeByte($byte));
        $this->assertSame($expectedData, $instance->toString());
    }

    /**
     * @return array<mixed>
     */
    public function provideShort(): array
    {
        return [
            [42, false, "\x2A"],
            [1, false, "\x01"],
            [0, false, "\x00"],
            [254, false, "\xFE"],
            [255, false, "\xFF\x00\xFF"],
            [256, false, "\xFF\x01\x00"],
            [65535, false, "\xFF\xFF\xFF"],
            [65536, true, ''],
            [-42, true, ''],
        ];
    }

    /**
     * @param int $short
     * @param bool $expectException
     * @param string $expectedData
     * @throws WriterException
     * @dataProvider provideShort
     */
    public function testShort(int $short, bool $expectException, string $expectedData): void
    {
        if ($expectException) {
            $this->expectException(WriterException::class);
        }

        $instance = new DataWriter();
        $this->assertSame($instance, $instance->writeShort($short));
        $this->assertSame($expectedData, $instance->toString());
    }

    public function testWriteId(): void
    {
        $id = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $expectedData = "\x2f\x4a\x45\xfa\xa5\x09\xa9\xd1\xaa\xe6\xff\xcf\x98\x4a\x7a\x76";

        $instance = new DataWriter();
        $this->assertSame($instance, $instance->writeId($id));
        $this->assertSame($expectedData, $instance->toString());
    }
}
