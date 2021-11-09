<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the PaginatedResultCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection
 */
class PaginatedResultCollectionTest extends TestCase
{
    public function testAddAndGetResults(): void
    {
        $result1 = $this->createMock(ResultInterface::class);
        $result2 = $this->createMock(ResultInterface::class);
        $result3 = $this->createMock(ResultInterface::class);
        $result4 = $this->createMock(ResultInterface::class);
        $result5 = $this->createMock(ResultInterface::class);
        $result6 = $this->createMock(ResultInterface::class);

        $instance = new PaginatedResultCollection();

        $this->assertSame(0, $instance->count());
        $this->assertSame([], $instance->getResults(0, 42));

        $this->assertSame($instance, $instance->add($result1));
        $this->assertSame(1, $instance->count());
        $this->assertSame([$result1], $instance->getResults(0, 42));

        $instance->add($result2)
                 ->add($result3)
                 ->add($result4)
                 ->add($result5)
                 ->add($result6);
        $this->assertSame(6, $instance->count());
        $this->assertSame([$result1, $result2, $result3, $result4, $result5, $result6], $instance->getResults(0, 42));
        $this->assertSame([$result3, $result4], $instance->getResults(2, 2));
    }

    public function testSetAndGetIsCached(): void
    {
        $instance = new PaginatedResultCollection();

        $this->assertSame($instance, $instance->setIsCached(true));
        $this->assertTrue($instance->getIsCached());
    }
}
