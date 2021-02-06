<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Serializer;

use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use FactorioItemBrowser\Api\Search\Serializer\DataReader;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the DataReader class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Serializer\DataReader
 */
class DataReaderTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideReadByte(): array
    {
        return [
            ["\x2A", false, 42],
            ["\x01", false, 1],
            ["\x00", false, 0],
            ["\xFF", false, 255],
            ["", true, 0],
        ];
    }

    /**
     * @param string $data
     * @param bool $expectException
     * @param int $expectedResult
     * @throws ReaderException
     * @dataProvider provideReadByte
     */
    public function testReadByte(string $data, bool $expectException, int $expectedResult): void
    {
        if ($expectException) {
            $this->expectException(ReaderException::class);
        }

        $instance = new DataReader($data);
        $result = $instance->readByte();
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array<mixed>
     */
    public function provideReadShort(): array
    {
        return [
            ["\x2A", false, 42],
            ["\x01", false, 1],
            ["\x00", false, 0],
            ["\xFE", false, 254],
            ["\xFF\x00\xFF", false, 255],
            ["\xFF\x01\x00", false, 256],
            ["\xFF\xFF\xFF", false, 65535],
            ["", true, 0],
            ["\xFF", true, 0],
            ["\xFF\x2A", true, 0],
        ];
    }

    /**
     * @param string $data
     * @param bool $expectException
     * @param int $expectedResult
     * @throws ReaderException
     * @dataProvider provideReadShort
     */
    public function testReadShort(string $data, bool $expectException, int $expectedResult): void
    {
        if ($expectException) {
            $this->expectException(ReaderException::class);
        }

        $instance = new DataReader($data);
        $result = $instance->readShort();
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReaderException
     */
    public function testReadId(): void
    {
        $data = "\x2f\x4a\x45\xfa\xa5\x09\xa9\xd1\xaa\xe6\xff\xcf\x98\x4a\x7a\x76";
        $expectedResult = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $instance = new DataReader($data);
        $result = $instance->readId();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @throws ReaderException
     */
    public function testHasUnreadData(): void
    {
        $reader = new DataReader("\x13\x37");
        $this->assertTrue($reader->hasUnreadData());
        $reader->readByte();
        $this->assertTrue($reader->hasUnreadData());
        $reader->readByte();
        $this->assertFalse($reader->hasUnreadData());
    }
}
