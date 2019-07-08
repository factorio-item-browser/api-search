<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\TermCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the Query class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Entity\Query
 */
class QueryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $queryString = 'abc';
        $modCombinationIds = [42, 1337];
        $locale = 'def';
        
        $query = new Query($queryString, $modCombinationIds, $locale);
        
        $this->assertSame($queryString, $this->extractProperty($query, 'queryString'));
        $this->assertSame($modCombinationIds, $this->extractProperty($query, 'modCombinationIds'));
        $this->assertSame($locale, $this->extractProperty($query, 'locale'));
        $this->assertInstanceOf(TermCollection::class, $this->extractProperty($query, 'terms'));
    }

    /**
     * Tests the setting and getting the query string.
     * @covers ::getQueryString
     * @covers ::setQueryString
     */
    public function testSetAndGetQueryString(): void
    {
        $queryString = 'abc';
        $query = new Query('foo', [42], 'bar');

        $this->assertSame($query, $query->setQueryString($queryString));
        $this->assertSame($queryString, $query->getQueryString());
    }

    /**
     * Tests the setting and getting the mod combination ids.
     * @covers ::getModCombinationIds
     * @covers ::setModCombinationIds
     */
    public function testSetAndGetModCombinationIds(): void
    {
        $modCombinationIds = [42, 1337];
        $query = new Query('foo', [], 'bar');

        $this->assertSame($query, $query->setModCombinationIds($modCombinationIds));
        $this->assertSame($modCombinationIds, $query->getModCombinationIds());
    }

    /**
     * Tests the setting and getting the locale.
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $query = new Query('foo', [], 'bar');

        $this->assertSame($query, $query->setLocale($locale));
        $this->assertSame($locale, $query->getLocale());
    }

    /**
     * Tests the addTerm method.
     * @throws ReflectionException
     * @covers ::addTerm
     */
    public function testAddTerm(): void
    {
        /* @var Term&MockObject $term */
        $term = $this->createMock(Term::class);

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('add')
                       ->with($this->identicalTo($term));

        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->addTerm($term);

        $this->assertSame($query, $result);
    }

    /**
     * Tests the getTerms method.
     * @throws ReflectionException
     * @covers ::getTerms
     */
    public function testGetTerms(): void
    {
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getAll')
                       ->willReturn($terms);


        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTerms();

        $this->assertSame($terms, $result);
    }

    /**
     * Tests the getTermValues method.
     * @throws ReflectionException
     * @covers ::getTermValues
     */
    public function testGetTermValues(): void
    {
        $values = ['abc', 'def'];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getAllValues')
                       ->willReturn($values);


        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTermValues();

        $this->assertSame($values, $result);
    }

    /**
     * Tests the getTermsByType method.
     * @throws ReflectionException
     * @covers ::getTermsByType
     */
    public function testGetTermsByType(): void
    {
        $type = 'abc';
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getByType')
                       ->with($this->identicalTo($type))
                       ->willReturn($terms);


        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTermsByType($type);

        $this->assertSame($terms, $result);
    }

    /**
     * Tests the getTermsByTypes method.
     * @throws ReflectionException
     * @covers ::getTermsByTypes
     */
    public function testGetTermsByTypes(): void
    {
        $types = ['abc', 'def'];
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getByTypes')
                       ->with($this->identicalTo($types))
                       ->willReturn($terms);


        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTermsByTypes($types);

        $this->assertSame($terms, $result);
    }

    /**
     * Tests the getTermValuesByType method.
     * @throws ReflectionException
     * @covers ::getTermValuesByType
     */
    public function testGetTermValuesByType(): void
    {
        $type = 'abc';
        $values = ['def', 'ghi'];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getValuesByType')
                       ->with($this->identicalTo($type))
                       ->willReturn($values);

        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTermValuesByType($type);

        $this->assertSame($values, $result);
    }
    
    /**
     * Tests the getTermValuesByTypes method.
     * @throws ReflectionException
     * @covers ::getTermValuesByTypes
     */
    public function testGetTermValuesByTypes(): void
    {
        $types = ['abc', 'def'];
        $values = ['ghi', 'jkl'];

        /* @var TermCollection&MockObject $termCollection */
        $termCollection = $this->createMock(TermCollection::class);
        $termCollection->expects($this->once())
                       ->method('getValuesByTypes')
                       ->with($this->identicalTo($types))
                       ->willReturn($values);

        $query = new Query('foo', [42], 'bar');
        $this->injectProperty($query, 'terms', $termCollection);
        $result = $query->getTermValuesByTypes($types);

        $this->assertSame($values, $result);
    }
    
    /**
     * Tests the setting and getting the hash.
     * @covers ::getHash
     * @covers ::setHash
     */
    public function testSetAndGetHash(): void
    {
        $hash = 'abc';
        $query = new Query('foo', [42], 'bar');

        $this->assertSame($query, $query->setHash($hash));
        $this->assertSame($hash, $query->getHash());
    }
}
