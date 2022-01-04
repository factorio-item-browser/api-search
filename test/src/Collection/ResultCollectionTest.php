<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use FactorioItemBrowser\Api\Search\Collection\ResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
        $item1->setId(Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1'));
        $item2 = new ItemResult();
        $item2->setId(Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e'));
        $item3 = new ItemResult();
        $item3->setId(Uuid::fromString('37451ec1-6b60-4870-a24f-41afd4cdd477'));

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
        $item1->setId(Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1'))
              ->setType('foo')
              ->setName('abc');
        $item2 = new ItemResult();
        $item2->setId(Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e'))
              ->setType('bar')
              ->setName('def');
        $item3 = new ItemResult();
        $item3->setId(Uuid::fromString('37451ec1-6b60-4870-a24f-41afd4cdd477'))
              ->setType('foo')
              ->setName('def');
        $item4 = new ItemResult();
        $item4->setId(Uuid::fromString('4a3a8c6c-8d68-41f6-997a-8761c380cd7d'))
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
        $item1->setId(Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1'))
              ->setType('foo')
              ->setName('abc');
        $item2 = new ItemResult();
        $item2->setId(Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e'))
              ->setType('foo')
              ->setName('def');
        $item3 = new ItemResult();
        $item3->setId(Uuid::fromString('37451ec1-6b60-4870-a24f-41afd4cdd477'))
              ->setType('bar')
              ->setName('abc');
        $item4 = new ItemResult();
        $item4->setId(Uuid::fromString('4a3a8c6c-8d68-41f6-997a-8761c380cd7d'))
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
