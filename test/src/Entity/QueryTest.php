<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Query class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Entity\Query
 */
class QueryTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new Query();

        // Asserted through type-hinting
        $instance->getTerms()->add(new Term('abc', 'def'));

        $this->addToAssertionCount(1);
    }

    public function testSetAndGetCombinationId(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $instance = new Query();

        $this->assertSame($instance, $instance->setCombinationId($combinationId));
        $this->assertSame($combinationId, $instance->getCombinationId());
    }

    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $instance = new Query();

        $this->assertSame($instance, $instance->setLocale($locale));
        $this->assertSame($locale, $instance->getLocale());
    }

    public function testSetAndGetQueryString(): void
    {
        $instanceString = 'abc';
        $instance = new Query();

        $this->assertSame($instance, $instance->setQueryString($instanceString));
        $this->assertSame($instanceString, $instance->getQueryString());
    }

    public function testSetAndGetHash(): void
    {
        $hash = $this->createMock(UuidInterface::class);
        $instance = new Query();

        $this->assertSame($instance, $instance->setHash($hash));
        $this->assertSame($hash, $instance->getHash());
    }
}
