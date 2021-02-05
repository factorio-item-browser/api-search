<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Exception;

use Exception;
use FactorioItemBrowser\Api\Search\Exception\WriterException;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the WriterException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Exception\WriterException
 */
class WriterExceptionTest extends TestCase
{
    public function test(): void
    {
        $message = 'abc';
        $previous = $this->createMock(Exception::class);

        $instance = new WriterException($message, $previous);

        $this->assertSame($message, $instance->getMessage());
        $this->assertSame(0, $instance->getCode());
        $this->assertSame($previous, $instance->getPrevious());
    }
}
