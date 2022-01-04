<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Service;

use DateTimeInterface;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Collection\PaginatedResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Service\SerializerService;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the CachedSearchResultService class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Service\CachedSearchResultService
 */
class CachedSearchResultServiceTest extends TestCase
{
    /** @var CachedSearchResultRepository&MockObject */
    private CachedSearchResultRepository $cachedSearchResultRepository;
    /** @var SerializerService&MockObject */
    private SerializerService $serializerService;
    private string $apiSearchMaxCacheAge = '2038-01-19T03:14:07+00:00';

    protected function setUp(): void
    {
        $this->cachedSearchResultRepository = $this->createMock(CachedSearchResultRepository::class);
        $this->serializerService = $this->createMock(SerializerService::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return CachedSearchResultService&MockObject
     */
    private function createInstance(array $mockedMethods = []): CachedSearchResultService
    {
        return $this->getMockBuilder(CachedSearchResultService::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->cachedSearchResultRepository,
                        $this->serializerService,
                        $this->apiSearchMaxCacheAge,
                    ])
                    ->getMock();
    }

    public function testGetResultsWithEntity(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $searchHash = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $locale = 'abc';
        $resultData = 'ghi';
        $paginatedResults = $this->createMock(PaginatedResultCollection::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        $entity = new CachedSearchResult();
        $entity->setResultData($resultData);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash),
                                           )
                                           ->willReturn($entity);
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('persist')
                                           ->with($this->identicalTo($entity));

        $this->serializerService->expects($this->once())
                                ->method('unserialize')
                                ->with($this->identicalTo($resultData))
                                ->willReturn($paginatedResults);

        $instance = $this->createInstance();
        $result = $instance->getResults($query);

        $this->assertSame($paginatedResults, $result);
    }

    public function testGetResultsWithoutEntity(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $searchHash = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $locale = 'abc';

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash),
                                           )
                                           ->willReturn(null);
        $this->cachedSearchResultRepository->expects($this->never())
                                           ->method('persist');

        $this->serializerService->expects($this->never())
                                ->method('unserialize');

        $instance = $this->createInstance();
        $result = $instance->getResults($query);

        $this->assertNull($result);
    }

    public function testGetResultsWithException(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $searchHash = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $locale = 'abc';
        $resultData = 'ghi';

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setHash($searchHash);

        $entity = new CachedSearchResult();
        $entity->setResultData($resultData);

        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('find')
                                           ->with(
                                               $this->identicalTo($combinationId),
                                               $this->identicalTo($locale),
                                               $this->identicalTo($searchHash),
                                           )
                                           ->willReturn($entity);
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('persist')
                                           ->with($this->identicalTo($entity));

        $this->serializerService->expects($this->once())
                                ->method('unserialize')
                                ->with($this->identicalTo($resultData))
                                ->willThrowException($this->createMock(Exception::class));

        $instance = $this->createInstance();
        $result = $instance->getResults($query);

        $this->assertNull($result);
    }

    public function testPersistResults(): void
    {
        $locale = 'abc';
        $searchQuery = 'def';
        $resultData = 'ghi';

        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $searchHash = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($searchQuery)
              ->setHash($searchHash);

        $expectedCachedSearchResult = new CachedSearchResult();
        $expectedCachedSearchResult->setCombinationId($combinationId)
                                   ->setLocale($locale)
                                   ->setSearchQuery($searchQuery)
                                   ->setSearchHash($searchHash)
                                   ->setResultData($resultData);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willReturn($resultData);

        $this->cachedSearchResultRepository
            ->expects($this->once())
            ->method('persist')
            ->with(new Callback(function (CachedSearchResult $csr) use ($expectedCachedSearchResult): bool {
                $expectedCachedSearchResult->setLastSearchTime($csr->getLastSearchTime());
                $this->assertEquals($expectedCachedSearchResult, $csr);
                return true;
            }));

        $instance = $this->createInstance();
        $instance->persistResults($query, $searchResults);
    }

    public function testPersistResultsWithException(): void
    {
        $locale = 'abc';
        $searchQuery = 'def';

        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $searchHash = Uuid::fromString('055b2276-16cc-4cc1-b5f0-56dd18c95553');
        $searchResults = $this->createMock(PaginatedResultCollection::class);

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale)
              ->setQueryString($searchQuery)
              ->setHash($searchHash);

        $this->serializerService->expects($this->once())
                                ->method('serialize')
                                ->with($this->identicalTo($searchResults))
                                ->willThrowException($this->createMock(Exception::class));

        $this->cachedSearchResultRepository->expects($this->never())
                                           ->method('persist');

        $instance = $this->createInstance();
        $instance->persistResults($query, $searchResults);
    }

    public function testClearExpiredResults(): void
    {
        $this->cachedSearchResultRepository
            ->expects($this->once())
            ->method('clearExpiredResults')
            ->with(new Callback(function (DateTimeInterface $dateTime): bool {
                return $dateTime->format('Y-m-d\TH:i:sP') === $this->apiSearchMaxCacheAge;
            }));

        $instance = $this->createInstance();
        $instance->clearExpiredResults();
    }

    public function testClearAll(): void
    {
        $this->cachedSearchResultRepository->expects($this->once())
                                           ->method('clearAll');

        $instance = $this->createInstance();
        $instance->clearAll();
    }
}
