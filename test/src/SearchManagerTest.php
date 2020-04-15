<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ResultInterface;
use FactorioItemBrowser\Api\Search\Service\FetcherService;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\SearchManager;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the SearchManager class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\SearchManager
 */
class SearchManagerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked cached search result service.
     * @var CachedSearchResultService&MockObject
     */
    protected $cachedSearchResultService;

    /**
     * The mocked fetcher service.
     * @var FetcherService&MockObject
     */
    protected $fetcherService;

    /**
     * The mocked query parser.
     * @var QueryParser&MockObject
     */
    protected $queryParser;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cachedSearchResultService = $this->createMock(CachedSearchResultService::class);
        $this->fetcherService = $this->createMock(FetcherService::class);
        $this->queryParser = $this->createMock(QueryParser::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $maxSearchResults = 42;
        $manager = new SearchManager(
            $this->cachedSearchResultService,
            $this->fetcherService,
            $this->queryParser,
            $maxSearchResults
        );

        $this->assertSame(
            $this->cachedSearchResultService,
            $this->extractProperty($manager, 'cachedSearchResultService')
        );
        $this->assertSame($this->fetcherService, $this->extractProperty($manager, 'fetcherService'));
        $this->assertSame($this->queryParser, $this->extractProperty($manager, 'queryParser'));
        $this->assertSame($maxSearchResults, $this->extractProperty($manager, 'maxSearchResults'));
    }

    /**
     * Tests the parseQuery method.
     * @covers ::parseQuery
     */
    public function testParseQuery(): void
    {
        $queryString = 'abc';
        $locale = 'def';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        $this->queryParser->expects($this->once())
                          ->method('parse')
                          ->with(
                              $this->identicalTo($combinationId),
                              $this->identicalTo($locale),
                              $this->identicalTo($queryString)
                          )
                          ->willReturn($query);

        $manager = new SearchManager($this->cachedSearchResultService, $this->fetcherService, $this->queryParser, 42);
        $result = $manager->parseQuery($combinationId, $locale, $queryString);

        $this->assertSame($query, $result);
    }

    /**
     * Tests the search method with a cached result.
     * @covers ::search
     */
    public function testSearchWithCachedResult(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var PaginatedResultCollection&MockObject $cachedResult */
        $cachedResult = $this->createMock(PaginatedResultCollection::class);

        $this->cachedSearchResultService->expects($this->once())
                                        ->method('getResults')
                                        ->with($this->identicalTo($query))
                                        ->willReturn($cachedResult);

        /* @var SearchManager&MockObject $manager */
        $manager = $this->getMockBuilder(SearchManager::class)
                        ->onlyMethods(['executeQuery'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultService,
                            $this->fetcherService,
                            $this->queryParser,
                            42
                        ])
                        ->getMock();
        $manager->expects($this->never())
                ->method('executeQuery');

        $result = $manager->search($query);

        $this->assertSame($cachedResult, $result);
    }

    /**
     * Tests the search method without a cached result.
     * @covers ::search
     */
    public function testSearchWithoutCachedResult(): void
    {
        $cachedResult = null;

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $this->cachedSearchResultService->expects($this->once())
                                        ->method('getResults')
                                        ->with($this->identicalTo($query))
                                        ->willReturn($cachedResult);

        $this->cachedSearchResultService->expects($this->once())
                                        ->method('persistResults')
                                        ->with($this->identicalTo($query), $this->identicalTo($searchResults));

        /* @var SearchManager&MockObject $manager */
        $manager = $this->getMockBuilder(SearchManager::class)
                        ->onlyMethods(['executeQuery'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultService,
                            $this->fetcherService,
                            $this->queryParser,
                            42
                        ])
                        ->getMock();
        $manager->expects($this->once())
                ->method('executeQuery')
                ->with($this->identicalTo($query))
                ->willReturn($searchResults);

        $result = $manager->search($query);

        $this->assertSame($searchResults, $result);
    }

    /**
     * Tests the executeQuery method.
     * @throws ReflectionException
     * @covers ::executeQuery
     */
    public function testExecuteQuery(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $this->fetcherService->expects($this->once())
                             ->method('fetch')
                             ->with(
                                 $this->identicalTo($query),
                                 $this->isInstanceOf(AggregatingResultCollection::class)
                             );

        /* @var SearchManager&MockObject $manager */
        $manager = $this->getMockBuilder(SearchManager::class)
                        ->onlyMethods(['createPaginatedCollection'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultService,
                            $this->fetcherService,
                            $this->queryParser,
                            42
                        ])
                        ->getMock();
        $manager->expects($this->once())
                ->method('createPaginatedCollection')
                ->with($this->isInstanceOf(AggregatingResultCollection::class))
                ->willReturn($searchResults);

        $result = $this->invokeMethod($manager, 'executeQuery', $query);

        $this->assertSame($searchResults, $result);
    }

    /**
     * Tests the createPaginatedCollection method.
     * @throws ReflectionException
     * @covers ::createPaginatedCollection
     */
    public function testCreatePaginatedCollection(): void
    {
        /* @var ResultInterface&MockObject $result1 */
        $result1 = $this->createMock(ResultInterface::class);
        /* @var ResultInterface&MockObject $result2 */
        $result2 = $this->createMock(ResultInterface::class);

        $expectedResult = new PaginatedResultCollection();
        $expectedResult->add($result1)
                       ->add($result2);

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('getMergedResults')
                      ->willReturn([$result1, $result2]);

        $manager = new SearchManager($this->cachedSearchResultService, $this->fetcherService, $this->queryParser, 42);
        $result = $this->invokeMethod($manager, 'createPaginatedCollection', $searchResults);

        $this->assertEquals($expectedResult, $result);
    }
}
