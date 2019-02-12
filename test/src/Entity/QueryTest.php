<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Entity;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
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
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $queryString = 'abc';
        
        $query = new Query($queryString);
        
        $this->assertSame($queryString, $query->getQueryString());
    }

    /**
     * Tests the setting and getting the query string.
     * @covers ::getQueryString
     * @covers ::setQueryString
     */
    public function testSetAndGetQueryString(): void
    {
        $queryString = 'abc';
        $query = new Query('foo');

        $this->assertSame($query, $query->setQueryString($queryString));
        $this->assertSame($queryString, $query->getQueryString());
    }

    /**
     * Tests the setting and getting the hash.
     * @covers ::getHash
     * @covers ::setHash
     */
    public function testSetAndGetHash(): void
    {
        $hash = 'abc';
        $query = new Query('foo');

        $this->assertSame($query, $query->setHash($hash));
        $this->assertSame($hash, $query->getHash());
    }

    /**
     * Provides the data for the addTerm test.
     * @return array
     */
    public function provideAddTerm(): array
    {
        $term1 = new Term('abc', 'def');
        $term2 = new Term('abc', 'ghi');
        $term3 = new Term('jkl', 'mno');

        return [
            [
                [],
                $term1,
                ['abc' => [$term1]],
            ],
            [
                ['abc' => [$term1]],
                $term2,
                ['abc' => [$term1, $term2]],
            ],
            [
                ['abc' => [$term1]],
                $term3,
                ['abc' => [$term1], 'jkl' => [$term3]],
            ],
        ];
    }

    /**
     * Tests the addTerm method.
     * @param array|Term[][] $terms
     * @param Term $term
     * @param array|Term[][] $expectedTerms
     * @throws ReflectionException
     * @covers ::addTerm
     * @dataProvider provideAddTerm
     */
    public function testAddTerm(array $terms, Term $term, array $expectedTerms): void
    {
        $query = new Query('foo');
        $this->injectProperty($query, 'terms', $terms);
        $result = $query->addTerm($term);

        $this->assertSame($query, $result);
        $this->assertSame($expectedTerms, $this->extractProperty($query, 'terms'));
    }

    /**
     * Provides the data for the getTerms test.
     * @return array
     */
    public function provideGetTerms(): array
    {
        $term1 = new Term('abc', 'def');
        $term2 = new Term('abc', 'ghi');
        $term3 = new Term('jkl', 'mno');

        return [
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                [$term1, $term2, $term3],
            ],
            [
                [],
                [],
            ],
        ];
    }

    /**
     * Tests the getTerms method.
     * @param array|Term[][] $terms
     * @param array|Term[] $expectedResult
     * @throws ReflectionException
     * @covers ::getTerms
     * @dataProvider provideGetTerms
     */
    public function testGetTerms(array $terms, array $expectedResult): void
    {
        $query = new Query('foo');
        $this->injectProperty($query, 'terms', $terms);
        $result = $query->getTerms();

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Provides the data for the getTermsByType test.
     * @return array
     */
    public function provideGetTermsByType(): array
    {
        $term1 = new Term('abc', 'def');
        $term2 = new Term('abc', 'ghi');
        $term3 = new Term('jkl', 'mno');

        return [
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                'abc',
                [$term1, $term2],
            ],
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                'pqr',
                [],
            ],
        ];
    }

    /**
     * Tests the getTermsByType method.
     * @param array|Term[][] $terms
     * @param string $type
     * @param array|Term[] $expectedResult
     * @throws ReflectionException
     * @covers ::getTermsByType
     * @dataProvider provideGetTermsByType
     */
    public function testGetTermsByType(array $terms, string $type, array $expectedResult): void
    {
        $query = new Query('foo');
        $this->injectProperty($query, 'terms', $terms);
        $result = $query->getTermsByType($type);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Provides the data for the getTermsByTypes test.
     * @return array
     */
    public function provideGetTermsByTypes(): array
    {
        $term1 = new Term('abc', 'def');
        $term2 = new Term('abc', 'ghi');
        $term3 = new Term('jkl', 'mno');

        return [
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                ['abc', 'jkl'],
                [$term1, $term2, $term3],
            ],
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                ['abc'],
                [$term1, $term2],
            ],
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                ['pqr'],
                [],
            ],
            [
                ['abc' => [$term1, $term2], 'jkl' => [$term3]],
                [],
                [],
            ],
        ];
    }

    /**
     * Tests the getTermsByTypes method.
     * @param array|Term[][] $terms
     * @param array|string[] $types
     * @param array|Term[] $expectedResult
     * @throws ReflectionException
     * @covers ::getTermsByTypes
     * @dataProvider provideGetTermsByTypes
     */
    public function testGetTermsByTypes(array $terms, array $types, array $expectedResult): void
    {
        $query = new Query('foo');
        $this->injectProperty($query, 'terms', $terms);
        $result = $query->getTermsByTypes($types);

        $this->assertSame($expectedResult, $result);
    }
}
