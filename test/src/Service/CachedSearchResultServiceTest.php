<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use BluePsyduck\TestHelper\ReflectionTrait;
use DateTimeImmutable;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CachedSearchResultService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Service\CachedSearchResultService
 */
class CachedSearchResultServiceTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked cached search result repository.
     * @var CachedSearchResultRepository&MockObject
     */
    protected $cachedSearchResultRepository;

    /**
     * The mocked serializer service.
     * @var SerializerService&MockObject
     */
    protected $serializerService;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cachedSearchResultRepository = $this->createMock(CachedSearchResultRepository::class);
        $this->serializerService = $this->createMock(SerializerService::class);
    }

    /**
     * Tests the constructing.
     * @throws Exception
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );

        $this->assertSame(
            $this->cachedSearchResultRepository,
            $this->extractProperty($service, 'cachedSearchResultRepository')
        );
        $this->assertSame($this->serializerService, $this->extractProperty($service, 'serializerService'));
        $this->assertInstanceOf(DateTimeImmutable::class, $this->extractProperty($service, 'maxCacheAge'));
    }

    /**
     * Tests the getResults method with an actual cache hit.
     * @covers ::getResults
     */
    public function testGetResultsWithHit(): void
    {
        $serializedResult = 'abc';

        /* @var PaginatedResultCollection&MockObject $searchResult */
        $searchResult = $this->createMock(PaginatedResultCollection::class);
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        $this->serializerService->expects($this->once())
                                ->method('unserialize')
                                ->with($this->identicalTo($serializedResult))
                                ->willReturn($searchResult);

        /* @var CachedSearchResultService&MockObject $service */
        $service = $this->getMockBuilder(CachedSearchResultService::class)
                        ->onlyMethods(['fetchSerializedResults'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultRepository,
                            $this->serializerService,
                            'today',
                        ])
                        ->getMock();
        $service->expects($this->once())
                ->method('fetchSerializedResults')
                ->with($this->identicalTo($query))
                ->willReturn($serializedResult);

        $result = $service->getResults($query);

        $this->assertSame($searchResult, $result);
    }

    /**
     * Tests the getResults method without an actual cache hit.
     * @covers ::getResults
     */
    public function testGetResultsWithoutHit(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        $this->serializerService->expects($this->never())
                                ->method('unserialize');

        /* @var CachedSearchResultService&MockObject $service */
        $service = $this->getMockBuilder(CachedSearchResultService::class)
                        ->onlyMethods(['fetchSerializedResults'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultRepository,
                            $this->serializerService,
                            'today',
                        ])
                        ->getMock();
        $service->expects($this->once())
                ->method('fetchSerializedResults')
                ->with($this->identicalTo($query))
                ->willReturn(null);

        $result = $service->getResults($query);

        $this->assertNull($result);
    }

    /**
     * Tests the fetchSerializedResults method.
     * @throws Exception
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResults(): void
    {
        $locale = 'abc';
        $resultData = 'def';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        /* @var DateTimeImmutable&MockObject $lastSearchTime */
        $lastSearchTime = $this->createMock(DateTimeImmutable::class);
        $lastSearchTime->expects($this->once())
                       ->method('getTimestamp')
                       ->willReturn(1337);

        /* @var DateTimeImmutable&MockObject $maxCacheAge */
        $maxCacheAge = $this->createMock(DateTimeImmutable::class);
        $maxCacheAge->expects($this->once())
                    ->method('getTimestamp')
                    ->willReturn(42);

        /* @var CachedSearchResult&MockObject $entity */
        $entity = $this->createMock(CachedSearchResult::class);
        $entity->expects($this->once())
               ->method('getLastSearchTime')
               ->willReturn($lastSearchTime);
        $entity->expects($this->once())
               ->method('getResultData')
               ->willReturn($resultData);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash)
                                           )
                                           ->willReturn($entity);
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('persist')
                                           ->with($this->identicalTo($entity));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $this->injectProperty($service, 'maxCacheAge', $maxCacheAge);

        $result = $this->invokeMethod($service, 'fetchSerializedResults', $query);

        $this->assertSame($resultData, $result);
    }

    /**
     * Tests the fetchSerializedResults method.
     * @throws Exception
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResultsWithExpiredEntity(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        /* @var DateTimeImmutable&MockObject $lastSearchTime */
        $lastSearchTime = $this->createMock(DateTimeImmutable::class);
        $lastSearchTime->expects($this->once())
                       ->method('getTimestamp')
                       ->willReturn(21);

        /* @var DateTimeImmutable&MockObject $maxCacheAge */
        $maxCacheAge = $this->createMock(DateTimeImmutable::class);
        $maxCacheAge->expects($this->once())
                    ->method('getTimestamp')
                    ->willReturn(42);

        /* @var CachedSearchResult&MockObject $entity */
        $entity = $this->createMock(CachedSearchResult::class);
        $entity->expects($this->once())
               ->method('getLastSearchTime')
               ->willReturn($lastSearchTime);
        $entity->expects($this->never())
               ->method('getResultData');

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash)
                                           )
                                           ->willReturn($entity);
        $this->cachedSearchResultRepository->expects($this->never())
                                           ->method('persist');

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $this->injectProperty($service, 'maxCacheAge', $maxCacheAge);

        $result = $this->invokeMethod($service, 'fetchSerializedResults', $query);

        $this->assertNull($result);
    }

    /**
     * Tests the fetchSerializedResults method.
     * @throws Exception
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResultsWithoutEntity(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash)
                                           )
                                           ->willReturn(null);
        $this->cachedSearchResultRepository->expects($this->never())
                                           ->method('persist');

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );

        $result = $this->invokeMethod($service, 'fetchSerializedResults', $query);

        $this->assertNull($result);
    }

    /**
     * Tests the fetchSerializedResults method.
     * @throws Exception
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResultsWithException(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash)
                                           )
                                           ->willThrowException($this->createMock(Exception::class));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $result = $this->invokeMethod($service, 'fetchSerializedResults', $query);

        $this->assertNull($result);
    }

    /**
     * Tests the persistResults method.
     * @throws Exception
     * @covers ::persistResults
     */
    public function testPersistResults(): void
    {
        $locale = 'abc';
        $searchQuery = 'def';
        $resultData = 'ghi';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);
        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($searchQuery)
              ->setHash($searchHash);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willReturn($resultData);

        $this->cachedSearchResultRepository
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (CachedSearchResult $entity) use (
                $combinationId,
                $locale,
                $searchQuery,
                $searchHash,
                $resultData
            ): bool {
                $this->assertSame($combinationId, $entity->getCombinationId());
                $this->assertSame($locale, $entity->getLocale());
                $this->assertSame($searchQuery, $entity->getSearchQuery());
                $this->assertSame($searchHash, $entity->getSearchHash());
                $this->assertSame($resultData, $entity->getResultData());
                return true;
            }));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $service->persistResults($query, $searchResults);
    }

    /**
     * Tests the persistResults method with throwing an exception.
     * @throws Exception
     * @covers ::persistResults
     */
    public function testPersistResultsWithException(): void
    {
        $locale = 'abc';
        $searchQuery = 'def';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($searchQuery)
              ->setHash($searchHash);

        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willThrowException(new Exception());

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $service->persistResults($query, $searchResults);
    }

    /**
     * Tests the clearExpiredResults method.
     * @throws Exception
     * @covers ::clearExpiredResults
     */
    public function testClearExpiredResults(): void
    {
        /* @var DateTimeImmutable&MockObject $maxCacheAge */
        $maxCacheAge = $this->createMock(DateTimeImmutable::class);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('clearExpiredResults')
                                           ->with($this->identicalTo($maxCacheAge));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $this->injectProperty($service, 'maxCacheAge', $maxCacheAge);

        $service->clearExpiredResults();
    }

    /**
     * Tests the clearAll method.
     * @throws Exception
     * @covers ::clearAll
     */
    public function testClearAll(): void
    {
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('clearAll');

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            'today'
        );
        $service->clearAll();
    }
}
