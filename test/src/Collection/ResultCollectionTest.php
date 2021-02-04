<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Collection\ResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ResultCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Collection\ResultCollection
 */
class ResultCollectionTest extends TestCase
{
    public function testAddWithoutKeys(): void
    {
        $item1 = new ItemResult();
        $item1->setId($this->createMock(UuidInterface::class));
        $item2 = new ItemResult();
        $item2->setId($this->createMock(UuidInterface::class));
        $item3 = new ItemResult();
        $item3->setId($this->createMock(UuidInterface::class));

        $instance = new ResultCollection();
        $result = $instance->add($item1)
                           ->add($item2)
                           ->add($item3);

        $this->assertSame($instance, $result);
        $this->assertSame([$item1, $item2, $item3], $instance->getAll());
    }

    public function testAddWithKeys(): void
    {
        $item1 = new ItemResult();
        $item1->setId($this->createMock(UuidInterface::class))
              ->setType('foo')
              ->setName('abc');
        $item2 = new ItemResult();
        $item2->setId($this->createMock(UuidInterface::class))
              ->setType('bar')
              ->setName('def');
        $item3 = new ItemResult();
        $item3->setId($this->createMock(UuidInterface::class))
              ->setType('foo')
              ->setName('def');
        $item4 = new ItemResult();
        $item4->setId($this->createMock(UuidInterface::class))
              ->setType('bar')
              ->setName('def');

        $instance = new ResultCollection();
        $result = $instance->add($item1)
                           ->add($item2)
                           ->add($item3)
                           ->add($item4);

        $this->assertSame($instance, $result);
        $this->assertSame([$item1, $item2, $item3], $instance->getAll());
    }

    public function testRemove(): void
    {
        $item1 = new ItemResult();
        $item1->setId($this->createMock(UuidInterface::class))
              ->setType('foo')
              ->setName('abc');
        $item2 = new ItemResult();
        $item2->setId($this->createMock(UuidInterface::class))
              ->setType('foo')
              ->setName('def');
        $item3 = new ItemResult();
        $item3->setId($this->createMock(UuidInterface::class))
              ->setType('bar')
              ->setName('abc');
        $item4 = new ItemResult();
        $item4->setId($this->createMock(UuidInterface::class))
              ->setType('bar')
              ->setName('def');

        $instance = new ResultCollection();
        $instance->add($item1)
                 ->add($item2)
                 ->add($item3);

        $this->assertSame([$item1, $item2, $item3], $instance->getAll());

        $this->assertSame($instance, $instance->remove($item2));
        $this->assertSame([$item1, $item3], $instance->getAll());

        $this->assertSame($instance, $instance->remove($item4));
        $this->assertSame([$item1, $item3], $instance->getAll());
    }
}
