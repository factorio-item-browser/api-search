<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search;

use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Fetcher\FetcherInterface;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\SearchManager;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the SearchManager class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\SearchManager
 */
class SearchManagerTest extends TestCase
{
    /** @var CachedSearchResultService&MockObject */
    private CachedSearchResultService $cachedSearchResultService;
    /** @var QueryParser&MockObject */
    private QueryParser $queryParser;
    /** @var array<FetcherInterface> */
    private array $fetchers = [];
    private int $maxSearchResults = 42;

    protected function setUp(): void
    {
        $this->cachedSearchResultService = $this->createMock(CachedSearchResultService::class);
        $this->queryParser = $this->createMock(QueryParser::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return SearchManager&MockObject
     */
    private function createInstance(array $mockedMethods = []): SearchManager
    {
        return $this->getMockBuilder(SearchManager::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->cachedSearchResultService,
                        $this->queryParser,
                        $this->fetchers,
                        $this->maxSearchResults,
                    ])
                    ->getMock();
    }

    public function testParse(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $locale = 'abc';
        $queryString = 'def';
        $query = $this->createMock(Query::class);

        $this->queryParser->expects($this->once())
                          ->method('parse')
                          ->with(
                              $this->identicalTo($combinationId),
                              $this->identicalTo($locale),
                              $this->identicalTo($queryString),
                          )
                          ->willReturn($query);

        $instance = $this->createInstance();
        $result = $instance->parseQuery($combinationId, $locale, $queryString);

        $this->assertSame($query, $result);
    }

    public function testSearchWithoutCachedResult(): void
    {
        $query = $this->createMock(Query::class);

        $callback = function ($searchResults): bool {
            $this->assertInstanceOf(AggregatingResultCollection::class, $searchResults);
            $searchResults->addItem(new ItemResult())
                          ->addItem(new ItemResult());
            return true;
        };

        $fetcher1 = $this->createMock(FetcherInterface::class);
        $fetcher1->expects($this->once())
                 ->method('fetch')
                 ->with($this->identicalTo($query), new Callback($callback));
        $fetcher2 = $this->createMock(FetcherInterface::class);
        $fetcher2->expects($this->once())
                 ->method('fetch')
                 ->with($this->identicalTo($query), new Callback($callback));

        $this->fetchers = [$fetcher1, $fetcher2];
        $this->maxSearchResults = 3;

        $this->cachedSearchResultService->expects($this->once())
                                        ->method('getResults')
                                        ->with($this->identicalTo($query))
                                        ->willReturn(null);
        $this->cachedSearchResultService->expects($this->once())
                                        ->method('persistResults')
                                        ->with(
                                            $this->identicalTo($query),
                                            $this->isInstanceOf(PaginatedResultCollection::class),
                                        );

        $instance = $this->createInstance();
        $result = $instance->search($query);

        $this->assertSame(3, $result->count());
    }

    public function testSearchWithCachedResults(): void
    {
        $query = $this->createMock(Query::class);

        $paginatedResults = $this->createMock(PaginatedResultCollection::class);
        $paginatedResults->expects($this->once())
                         ->method('setIsCached')
                         ->with($this->identicalTo(true))
                         ->willReturnSelf();

        $fetcher1 = $this->createMock(FetcherInterface::class);
        $fetcher1->expects($this->never())
                 ->method('fetch');
        $fetcher2 = $this->createMock(FetcherInterface::class);
        $fetcher2->expects($this->never())
                 ->method('fetch');

        $this->fetchers = [$fetcher1, $fetcher2];

        $this->cachedSearchResultService->expects($this->once())
                                        ->method('getResults')
                                        ->with($this->identicalTo($query))
                                        ->willReturn($paginatedResults);
        $this->cachedSearchResultService->expects($this->never())
                                        ->method('persistResults');

        $instance = $this->createInstance();
        $result = $instance->search($query);

        $this->assertSame($paginatedResults, $result);
    }
}
