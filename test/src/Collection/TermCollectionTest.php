<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Collection\TermCollection;
use FactorioItemBrowser\Api\Search\Entity\Term;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TermCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Collection\TermCollection
 */
class TermCollectionTest extends TestCase
{
    public function test(): void
    {
        $term1 = new Term('foo', 'abc');
        $term2 = new Term('foo', 'def');
        $term3 = new Term('bar', 'abc');
        $term4 = new Term('bar', 'ghi');

        $instance = new TermCollection();

        $result = $instance->add($term1)
                           ->add($term2)
                           ->add($term3)
                           ->add($term4);
        $this->assertSame($instance, $result);

        $this->assertEquals([$term1, $term2, $term3, $term4], $instance->getAll());
        $this->assertEquals(['abc', 'def', 'ghi'], $instance->getAllValues());
        $this->assertEquals([$term1, $term2], $instance->getByTypes(['foo']));
        $this->assertEquals(['abc', 'def'], $instance->getValuesByTypes(['foo']));
    }
}
