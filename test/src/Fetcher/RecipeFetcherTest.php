<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Entity\Term;
use FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the RecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher
 */
class RecipeFetcherTest extends TestCase
{
    /** @var MapperManagerInterface&MockObject */
    private MapperManagerInterface $mapperManager;
    /** @var RecipeRepository&MockObject */
    private RecipeRepository $recipeRepository;

    protected function setUp(): void
    {
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return RecipeFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): RecipeFetcher
    {
        return $this->getMockBuilder(RecipeFetcher::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->mapperManager,
                        $this->recipeRepository,
                    ])
                    ->getMock();
    }

    public function testFetch(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $recipe1 = $this->createMock(RecipeData::class);
        $recipe2 = $this->createMock(RecipeData::class);

        $query = new Query();
        $query->setCombinationId($combinationId);
        $query->getTerms()->add(new Term(TermType::GENERIC, 'abc'));

        $recipeResult1 = $this->createMock(RecipeResult::class);
        $recipeResult1->expects($this->once())
                      ->method('setPriority')
                      ->with($this->identicalTo(SearchResultPriority::EXACT_MATCH));

        $recipeResult2 = $this->createMock(RecipeResult::class);
        $recipeResult2->expects($this->once())
                      ->method('setPriority')
                      ->with($this->identicalTo(SearchResultPriority::EXACT_MATCH));

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($recipeResult1)],
                          [$this->identicalTo($recipeResult2)],
                      );

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByKeywords')
                               ->with($this->identicalTo($combinationId), $this->equalTo(['abc']))
                               ->willReturn([$recipe1, $recipe2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($recipe1), $this->isInstanceOf(RecipeResult::class)],
                                [$this->identicalTo($recipe2), $this->isInstanceOf(RecipeResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $recipeResult1,
                                $recipeResult2
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);
    }
}
