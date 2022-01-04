<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Repository\TranslationRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Term;
use FactorioItemBrowser\Api\Search\Fetcher\TranslationFetcher;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the TranslationFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\TranslationFetcher
 */
class TranslationFetcherTest extends TestCase
{
    /** @var MapperManagerInterface&MockObject */
    private MapperManagerInterface $mapperManager;
    /** @var TranslationRepository&MockObject */
    private TranslationRepository $translationRepository;

    protected function setUp(): void
    {
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
        $this->translationRepository = $this->createMock(TranslationRepository::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return TranslationFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): TranslationFetcher
    {
        return $this->getMockBuilder(TranslationFetcher::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->mapperManager,
                        $this->translationRepository,
                    ])
                    ->getMock();
    }

    public function testFetch(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $locale = 'foo';

        $query = new Query();
        $query->setCombinationId($combinationId)
              ->setLocale($locale);
        $query->getTerms()->add(new Term(TermType::GENERIC, 'bar'));

        $translation1 = new TranslationPriorityData();
        $translation1->setType(EntityType::ITEM)
                     ->setName('abc');
        $translation2 = new TranslationPriorityData();
        $translation2->setType(EntityType::ITEM)
                     ->setName('def');
        $translation3 = new TranslationPriorityData();
        $translation3->setType(EntityType::RECIPE)
                     ->setName('ghi');
        $translation4 = new TranslationPriorityData();
        $translation4->setType(EntityType::RECIPE)
                     ->setName('jkl');

        $item1 = $this->createMock(ItemResult::class);
        $item2 = $this->createMock(ItemResult::class);
        $recipe1 = $this->createMock(RecipeResult::class);
        $recipe2 = $this->createMock(RecipeResult::class);

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addItem')
                      ->withConsecutive(
                          [$this->identicalTo($item1)],
                          [$this->identicalTo($item2)],
                      );
        $searchResults->expects($this->exactly(2))
                      ->method('addRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($recipe1)],
                          [$this->identicalTo($recipe2)]
                      );

        $this->translationRepository->expects($this->once())
                                    ->method('findDataByKeywords')
                                    ->with(
                                        $this->identicalTo($combinationId),
                                        $this->identicalTo('foo'),
                                        $this->identicalTo(['bar']),
                                    )
                                    ->willReturn([$translation1, $translation2, $translation3, $translation4]);

        $this->mapperManager->expects($this->exactly(4))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($translation1), $this->isInstanceOf(ItemResult::class)],
                                [$this->identicalTo($translation2), $this->isInstanceOf(ItemResult::class)],
                                [$this->identicalTo($translation3), $this->isInstanceOf(RecipeResult::class)],
                                [$this->identicalTo($translation4), $this->isInstanceOf(RecipeResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $item1,
                                $item2,
                                $recipe1,
                                $recipe2,
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);
    }
}
