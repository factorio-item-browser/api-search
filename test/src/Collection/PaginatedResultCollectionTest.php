<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Collection;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the PaginatedResultCollection class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection
 */
class PaginatedResultCollectionTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $collection = new PaginatedResultCollection();

        $this->assertSame([], $this->extractProperty($collection, 'results'));
    }

    /**
     * Tests the add method.
     * @throws ReflectionException
     * @covers ::add
     */
    public function testAdd(): void
    {
        /* @var ResultInterface&MockObject $result1 */
        $result1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result2 */
        $result2 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $newResult */
        $newResult = $this->createMock(ResultInterface::class);

        $results = [$result1, $result2];
        $expectedResults = [$result1, $result2, $newResult];

        $collection = new PaginatedResultCollection();
        $this->injectProperty($collection, 'results', $results);

        $result = $collection->add($newResult);

        $this->assertSame($collection, $result);
        $this->assertEquals($expectedResults, $this->extractProperty($collection, 'results'));
    }

    /**
     * Tests the count method.
     * @throws ReflectionException
     * @covers ::count
     */
    public function testCount(): void
    {
        /* @var ResultInterface&MockObject $result1 */
        $result1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result2 */
        $result2 = $this->createMock(ResultInterface::class);

        $results = [$result1, $result2];
        $expectedResult = 2;

        $collection = new PaginatedResultCollection();
        $this->injectProperty($collection, 'results', $results);

        $result = $collection->count();

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getResults method.
     * @throws ReflectionException
     * @covers ::getResults
     */
    public function testGetResults(): void
    {
        /* @var ResultInterface&MockObject $result1 */
        $result1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result2 */
        $result2 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result3 */
        $result3 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result4 */
        $result4 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result5 */
        $result5 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result6 */
        $result6 = $this->createMock(ResultInterface::class);

        $results = [$result1, $result2, $result3, $result4, $result5, $result6];
        $expectedResult = [$result3, $result4];

        $collection = new PaginatedResultCollection();
        $this->injectProperty($collection, 'results', $results);

        $result = $collection->getResults(2, 2);

        $this->assertSame($expectedResult, $result);
    }
}
