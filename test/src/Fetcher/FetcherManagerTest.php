<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the FetcherManager class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\FetcherManager
 */
class FetcherManagerTest extends TestCase
{
    /**
     * Tests the fetch method.
     * @throws ReflectionException
     * @covers ::__construct
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);

        /* @var FetcherInterface&MockObject $fetcher1 */
        $fetcher1 = $this->createMock(FetcherInterface::class);
        $fetcher1->expects($this->once())
                 ->method('fetch')
                 ->with($this->identicalTo($query), $this->identicalTo($searchResults));

        /* @var FetcherInterface&MockObject $fetcher2 */
        $fetcher2 = $this->createMock(FetcherInterface::class);
        $fetcher2->expects($this->once())
                 ->method('fetch')
                 ->with($this->identicalTo($query), $this->identicalTo($searchResults));

        $fetchers = [$fetcher1, $fetcher2];

        $manager = new FetcherManager($fetchers);
        $manager->fetch($query, $searchResults);
    }
}
