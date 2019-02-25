<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use BluePsyduck\Common\Test\ReflectionTrait;
use DateTime;
use DateTimeInterface;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

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
     * The mocked max cache age.
     * @var DateTimeInterface&MockObject
     */
    protected $maxCacheAge;

    /**
     * Sets up the test case.
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cachedSearchResultRepository = $this->createMock(CachedSearchResultRepository::class);
        $this->serializerService = $this->createMock(SerializerService::class);
        $this->maxCacheAge = $this->createMock(DateTime::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );

        $this->assertSame(
            $this->cachedSearchResultRepository,
            $this->extractProperty($service, 'cachedSearchResultRepository')
        );
        $this->assertSame($this->serializerService, $this->extractProperty($service, 'serializerService'));
        $this->assertSame($this->maxCacheAge, $this->extractProperty($service, 'maxCacheAge'));
    }

    /**
     * Tests the getResults method with an actual cache hit.
     * @throws ReflectionException
     * @covers ::getResults
     */
    public function testGetResultsWithHit(): void
    {
        $hash = 'ab12cd34';
        $serializedResult = 'abc';

        /* @var PaginatedResultCollection&MockObject $searchResult */
        $searchResult = $this->createMock(PaginatedResultCollection::class);

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getHash')
              ->willReturn($hash);

        $this->serializerService->expects($this->once())
                                ->method('unserialize')
                                ->with($this->identicalTo($serializedResult))
                                ->willReturn($searchResult);

        /* @var CachedSearchResultService&MockObject $service */
        $service = $this->getMockBuilder(CachedSearchResultService::class)
                        ->setMethods(['fetchSerializedResults'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultRepository,
                            $this->serializerService,
                            $this->maxCacheAge,
                        ])
                        ->getMock();
        $service->expects($this->once())
                ->method('fetchSerializedResults')
                ->with($this->identicalTo($hash))
                ->willReturn($serializedResult);

        $result = $service->getResults($query);

        $this->assertSame($searchResult, $result);
    }

    /**
     * Tests the getResults method without an actual cache hit.
     * @throws ReflectionException
     * @covers ::getResults
     */
    public function testGetResultsWithoutHit(): void
    {
        $hash = 'ab12cd34';
        $searchResult = null;

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getHash')
              ->willReturn($hash);

        $this->serializerService->expects($this->never())
                                ->method('unserialize');

        /* @var CachedSearchResultService&MockObject $service */
        $service = $this->getMockBuilder(CachedSearchResultService::class)
                        ->setMethods(['fetchSerializedResults'])
                        ->setConstructorArgs([
                            $this->cachedSearchResultRepository,
                            $this->serializerService,
                            $this->maxCacheAge,
                        ])
                        ->getMock();
        $service->expects($this->once())
                ->method('fetchSerializedResults')
                ->with($this->identicalTo($hash))
                ->willReturn($searchResult);

        $result = $service->getResults($query);

        $this->assertNull($result);
    }

    /**
     * Tests the fetchSerializedResults method.
     * @throws ReflectionException
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResults(): void
    {
        $hash = 'ab12cd34';
        $resultData = 'abc';

        /* @var CachedSearchResult&MockObject $entity1 */
        $entity1 = $this->createMock(CachedSearchResult::class);
        $entity1->expects($this->once())
                ->method('getResultData')
                ->willReturn($resultData);

        /* @var CachedSearchResult&MockObject $entity2 */
        $entity2 = $this->createMock(CachedSearchResult::class);
        $entity2->expects($this->never())
                ->method('getResultData');

        $entities = [$entity1, $entity2];

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('findByHashes')
                                           ->with($this->identicalTo([$hash]), $this->identicalTo($this->maxCacheAge))
                                           ->willReturn($entities);

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $result = $this->invokeMethod($service, 'fetchSerializedResults', $hash);

        $this->assertSame($resultData, $result);
    }

    /**
     * Tests the fetchSerializedResults method without an actual result.
     * @throws ReflectionException
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResultsWithoutResult(): void
    {
        $hash = 'ab12cd34';
        $entities = [];

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('findByHashes')
                                           ->with($this->identicalTo([$hash]), $this->identicalTo($this->maxCacheAge))
                                           ->willReturn($entities);

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $result = $this->invokeMethod($service, 'fetchSerializedResults', $hash);

        $this->assertNull($result);
    }

    /**
     * Tests the fetchSerializedResults method with throwing an exception.
     * @throws ReflectionException
     * @covers ::fetchSerializedResults
     */
    public function testFetchSerializedResultsWithException(): void
    {
        $hash = 'ab12cd34';

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('findByHashes')
                                           ->with($this->identicalTo([$hash]), $this->identicalTo($this->maxCacheAge))
                                           ->willThrowException(new Exception());

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $result = $this->invokeMethod($service, 'fetchSerializedResults', $hash);

        $this->assertNull($result);
    }

    /**
     * Tests the persistResults method.
     * @throws ReflectionException
     * @covers ::persistResults
     */
    public function testPersistResults(): void
    {
        $hash = 'ab12cd34';
        $resultData = 'abc';

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getHash')
              ->willReturn($hash);

        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willReturn($resultData);

        $this->cachedSearchResultRepository
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (CachedSearchResult $entity) use ($hash, $resultData): bool {
                $this->assertSame($hash, $entity->getHash());
                $this->assertSame($resultData, $entity->getResultData());
                return true;
            }));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $service->persistResults($query, $searchResults);
    }

    /**
     * Tests the persistResults method with throwing an exception.
     * @throws ReflectionException
     * @covers ::persistResults
     */
    public function testPersistResultsWithException(): void
    {
        $hash = 'ab12cd34';

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getHash')
              ->willReturn($hash);

        /* @var PaginatedResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willThrowException(new Exception());

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $service->persistResults($query, $searchResults);
    }

    /**
     * Tests the cleanCache method.
     * @covers ::cleanCache
     */
    public function testCleanCache(): void
    {
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('cleanup')
                                           ->with($this->identicalTo($this->maxCacheAge));

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $service->cleanCache();
    }

    /**
     * Tests the clearCache method.
     * @covers ::clearCache
     */
    public function testClearCache(): void
    {
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('clear');

        $service = new CachedSearchResultService(
            $this->cachedSearchResultRepository,
            $this->serializerService,
            $this->maxCacheAge
        );
        $service->clearCache();
    }
}
