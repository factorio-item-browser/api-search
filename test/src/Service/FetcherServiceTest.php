<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Service\FetcherService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the FetcherService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Service\FetcherService
 */
class FetcherServiceTest extends TestCase
{
    /**
     * Tests the fetch method.
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

        $service = new FetcherService($fetchers);
        $service->fetch($query, $searchResults);
    }
}
