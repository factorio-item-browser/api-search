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
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\Term
 */
class TermTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $type = 'abc';
        $value = 'def';

        $term = new Term($type, $value);

        $this->assertSame($type, $term->getType());
        $this->assertSame($value, $term->getValue());
    }

    /**
     * Tests the setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $term = new Term('foo', 'bar');

        $this->assertSame($term, $term->setType($type));
        $this->assertSame($type, $term->getType());
    }

    /**
     * Tests the setting and getting the value.
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testSetAndGetValue(): void
    {
        $value = 'abc';
        $term = new Term('foo', 'bar');

        $this->assertSame($term, $term->setValue($value));
        $this->assertSame($value, $term->getValue());
    }
}
