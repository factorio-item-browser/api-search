<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\TermCollection;
use FactorioItemBrowser\Api\Search\Entity\Term;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the TermCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Collection\TermCollection
 */
class TermCollectionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $collection = new TermCollection();

        $this->assertSame([], $this->extractProperty($collection, 'termsByType'));
    }

    /**
     * Provides the data for the add test.
     * @return array<mixed>
     */
    public function provideAdd(): array
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
     * Tests the add method.
     * @param array|Term[][] $termsByType
     * @param Term $term
     * @param array|Term[][] $expectedTermsByType
     * @throws ReflectionException
     * @covers ::add
     * @dataProvider provideAdd
     */
    public function testAdd(array $termsByType, Term $term, array $expectedTermsByType): void
    {
        $collection = new TermCollection();
        $this->injectProperty($collection, 'termsByType', $termsByType);
        $result = $collection->add($term);

        $this->assertSame($collection, $result);
        $this->assertSame($expectedTermsByType, $this->extractProperty($collection, 'termsByType'));
    }

    /**
     * Provides the data for the getAll test.
     * @return array<mixed>
     */
    public function provideGetAll(): array
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
     * Tests the getAll method.
     * @param array|Term[][] $termsByType
     * @param array|Term[] $expectedResult
     * @throws ReflectionException
     * @covers ::getAll
     * @dataProvider provideGetAll
     */
    public function testGetAll(array $termsByType, array $expectedResult): void
    {
        $collection = new TermCollection();
        $this->injectProperty($collection, 'termsByType', $termsByType);
        $result = $collection->getAll();

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getAllValues method.
     * @covers ::getAllValues
     */
    public function testGetAllValues(): void
    {
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];
        $values = ['abc', 'def'];

        /* @var TermCollection&MockObject $collection */
        $collection = $this->getMockBuilder(TermCollection::class)
                           ->onlyMethods(['getAll','getValues'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getAll')
                   ->willReturn($terms);
        $collection->expects($this->once())
                   ->method('getValues')
                   ->with($this->identicalTo($terms))
                   ->willReturn($values);

        $result = $collection->getAllValues();

        $this->assertSame($values, $result);
    }

    /**
     * Provides the data for the getByTypes test.
     * @return array<mixed>
     */
    public function provideGetByTypes(): array
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
     * Tests the getByTypes method.
     * @param array|Term[][] $termsByType
     * @param array|string[] $types
     * @param array|Term[] $expectedResult
     * @throws ReflectionException
     * @covers ::getByTypes
     * @dataProvider provideGetByTypes
     */
    public function testGetByTypes(array $termsByType, array $types, array $expectedResult): void
    {
        $collection = new TermCollection();
        $this->injectProperty($collection, 'termsByType', $termsByType);
        $result = $collection->getByTypes($types);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getByType method.
     * @covers ::getByType
     */
    public function testGetByType(): void
    {
        $type = 'abc';
        $expectedTypes = ['abc'];
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];

        /* @var TermCollection&MockObject $collection */
        $collection = $this->getMockBuilder(TermCollection::class)
                           ->onlyMethods(['getByTypes'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getByTypes')
                   ->with($this->identicalTo($expectedTypes))
                   ->willReturn($terms);

        $result = $collection->getByType($type);

        $this->assertSame($terms, $result);
    }

    /**
     * Tests the getValuesByTypes method.
     * @covers ::getValuesByTypes
     */
    public function testGetValuesByTypes(): void
    {
        $types = ['abc', 'def'];
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];
        $values = ['ghi', 'jkl'];

        /* @var TermCollection&MockObject $collection */
        $collection = $this->getMockBuilder(TermCollection::class)
                           ->onlyMethods(['getByTypes','getValues'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getByTypes')
                   ->with($this->identicalTo($types))
                   ->willReturn($terms);
        $collection->expects($this->once())
                   ->method('getValues')
                   ->with($this->identicalTo($terms))
                   ->willReturn($values);

        $result = $collection->getValuesByTypes($types);

        $this->assertSame($values, $result);
    }

    /**
     * Tests the getValuesByType method.
     * @covers ::getValuesByType
     */
    public function testGetValuesByType(): void
    {
        $type = 'abc';
        $terms = [
            $this->createMock(Term::class),
            $this->createMock(Term::class),
        ];
        $values = ['def', 'ghi'];

        /* @var TermCollection&MockObject $collection */
        $collection = $this->getMockBuilder(TermCollection::class)
                           ->onlyMethods(['getByType','getValues'])
                           ->getMock();
        $collection->expects($this->once())
                   ->method('getByType')
                   ->with($this->identicalTo($type))
                   ->willReturn($terms);
        $collection->expects($this->once())
                   ->method('getValues')
                   ->with($this->identicalTo($terms))
                   ->willReturn($values);

        $result = $collection->getValuesByType($type);

        $this->assertSame($values, $result);
    }

    /**
     * Tests the getValues method.
     * @throws ReflectionException
     * @covers ::getValues
     */
    public function testGetValues(): void
    {
        /* @var Term&MockObject $term1 */
        $term1 = $this->createMock(Term::class);
        $term1->expects($this->once())
              ->method('getValue')
              ->willReturn('abc');

        /* @var Term&MockObject $term2 */
        $term2 = $this->createMock(Term::class);
        $term2->expects($this->once())
              ->method('getValue')
              ->willReturn('def');

        $terms = [$term1, $term2];
        $expectedReuslt = ['abc', 'def'];

        $collection = new TermCollection();
        $result = $this->invokeMethod($collection, 'getValues', $terms);

        $this->assertEquals($expectedReuslt, $result);
    }
}
