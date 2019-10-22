<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\TestHelper\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Repository\TranslationRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\TranslationFetcher;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the TranslationFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\TranslationFetcher
 */
class TranslationFetcherTest extends TestCase
{
    use ReflectionTrait;

    /**
     * The mocked mapper manager.
     * @var MapperManagerInterface&MockObject
     */
    protected $mapperManager;

    /**
     * The mocked translation repository.
     * @var TranslationRepository&MockObject
     */
    protected $translationRepository;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
        $this->translationRepository = $this->createMock(TranslationRepository::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $fetcher = new TranslationFetcher($this->mapperManager, $this->translationRepository);

        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
        $this->assertSame($this->translationRepository, $this->extractProperty($fetcher, 'translationRepository'));
    }

    /**
     * Tests the fetch method.
     * @throws MapperException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        /* @var ItemResult&MockObject $item */
        $item = $this->createMock(ItemResult::class);
        /* @var RecipeResult&MockObject $recipe */
        $recipe = $this->createMock(RecipeResult::class);

        /* @var TranslationPriorityData&MockObject $translation1 */
        $translation1 = $this->createMock(TranslationPriorityData::class);
        $translation1->expects($this->once())
                     ->method('getType')
                     ->willReturn(EntityType::RECIPE);

        /* @var TranslationPriorityData&MockObject $translation2 */
        $translation2 = $this->createMock(TranslationPriorityData::class);
        $translation2->expects($this->once())
                     ->method('getType')
                     ->willReturn(EntityType::ITEM);

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->once())
                      ->method('addRecipe')
                      ->with($this->identicalTo($recipe));
        $searchResults->expects($this->once())
                      ->method('addItem')
                      ->with($this->identicalTo($item));

        $translations = [$translation1, $translation2];

        /* @var TranslationFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(TranslationFetcher::class)
                        ->onlyMethods(['fetchTranslations', 'mapTranslationToItem', 'mapTranslationToRecipe'])
                        ->disableOriginalConstructor()
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('fetchTranslations')
                ->with($this->identicalTo($query))
                ->willReturn($translations);
        $fetcher->expects($this->once())
                ->method('mapTranslationToRecipe')
                ->with($this->identicalTo($translation1))
                ->willReturn($recipe);
        $fetcher->expects($this->once())
                ->method('mapTranslationToItem')
                ->with($this->identicalTo($translation2))
                ->willReturn($item);

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the fetchTranslations method.
     * @throws ReflectionException
     * @covers ::fetchTranslations
     */
    public function testFetchTranslations(): void
    {
        $locale = 'abc';
        $keywords = ['def', 'ghi'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var TranslationPriorityData&MockObject $translation1 */
        $translation1 = $this->createMock(TranslationPriorityData::class);
        /* @var TranslationPriorityData&MockObject $translation2 */
        $translation2 = $this->createMock(TranslationPriorityData::class);

        $translations = [$translation1, $translation2];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getCombinationId')
              ->willReturn($combinationId);
        $query->expects($this->once())
              ->method('getLocale')
              ->willReturn($locale);
        $query->expects($this->once())
              ->method('getTermValuesByType')
              ->willReturn($keywords);

        $this->translationRepository->expects($this->once())
                                    ->method('findDataByKeywords')
                                    ->with(
                                        $this->identicalTo($combinationId),
                                        $this->identicalTo($locale),
                                        $this->identicalTo($keywords)
                                    )
                                    ->willReturn($translations);

        $fetcher = new TranslationFetcher($this->mapperManager, $this->translationRepository);
        $result = $this->invokeMethod($fetcher, 'fetchTranslations', $query);

        $this->assertSame($translations, $result);
    }
    
    /**
     * Tests the mapTranslationToItem method.
     * @throws ReflectionException
     * @covers ::mapTranslationToItem
     */
    public function testMapTranslationToItem(): void
    {
        /* @var TranslationPriorityData&MockObject $translation */
        $translation = $this->createMock(TranslationPriorityData::class);
        
        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($translation), $this->isInstanceOf(ItemResult::class));

        $fetcher = new TranslationFetcher($this->mapperManager, $this->translationRepository);
        $this->invokeMethod($fetcher, 'mapTranslationToItem', $translation);
    }
    
    /**
     * Tests the mapTranslationToRecipe method.
     * @throws ReflectionException
     * @covers ::mapTranslationToRecipe
     */
    public function testMapTranslationToRecipe(): void
    {
        /* @var TranslationPriorityData&MockObject $translation */
        $translation = $this->createMock(TranslationPriorityData::class);
        
        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($translation), $this->isInstanceOf(RecipeResult::class));

        $fetcher = new TranslationFetcher($this->mapperManager, $this->translationRepository);
        $this->invokeMethod($fetcher, 'mapTranslationToRecipe', $translation);
    }
}
