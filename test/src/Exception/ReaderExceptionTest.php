<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Exception;

use Exception;
use FactorioItemBrowser\Api\Search\Exception\ReaderException;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ReaderException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Exception\ReaderException
 */
class ReaderExceptionTest extends TestCase
{
    public function test(): void
    {
        $message = 'abc';
        $previous = $this->createMock(Exception::class);

        $instance = new ReaderException($message, $previous);

        $this->assertSame($message, $instance->getMessage());
        $this->assertSame(0, $instance->getCode());
        $this->assertSame($previous, $instance->getPrevious());
    }
}
