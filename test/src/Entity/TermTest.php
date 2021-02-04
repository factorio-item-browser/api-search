<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use FactorioItemBrowser\Api\Search\Entity\Term;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Term class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Entity\Term
 */
class TermTest extends TestCase
{
    public function testConstruct(): void
    {
        $type = 'abc';
        $value = 'def';

        $instance = new Term($type, $value);

        $this->assertSame($type, $instance->getType());
        $this->assertSame($value, $instance->getValue());
    }

    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $instance = new Term('foo', 'bar');

        $this->assertSame($instance, $instance->setType($type));
        $this->assertSame($type, $instance->getType());
    }

    public function testSetAndGetValue(): void
    {
        $value = 'abc';
        $instance = new Term('foo', 'bar');

        $this->assertSame($instance, $instance->setValue($value));
        $this->assertSame($value, $instance->getValue());
    }
}
