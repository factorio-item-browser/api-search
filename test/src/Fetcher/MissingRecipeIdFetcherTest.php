<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the MissingRecipeIdFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\MissingRecipeIdFetcher
 */
class MissingRecipeIdFetcherTest extends TestCase
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
     * @return MissingRecipeIdFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): MissingRecipeIdFetcher
    {
        return $this->getMockBuilder(MissingRecipeIdFetcher::class)
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

        $recipeResult1 = new RecipeResult();
        $recipeResult1->setName('abc');
        $recipeResult2 = new RecipeResult();
        $recipeResult2->setName('def')
                      ->setNormalRecipeId(Uuid::fromString('11b19ed3-e772-44b1-9938-2cca1c63c7a1'))
                      ->setExpensiveRecipeId(Uuid::fromString('24db0d5a-a933-4e46-bb5a-0b7d88c6272e'));
        $recipeResult3 = new RecipeResult();
        $recipeResult3->setName('ghi')
                      ->setNormalRecipeId(Uuid::fromString('37451ec1-6b60-4870-a24f-41afd4cdd477'));
        $recipeResult4 = new RecipeResult();
        $recipeResult4->setName('jkl')
                      ->setExpensiveRecipeId(Uuid::fromString('4a3a8c6c-8d68-41f6-997a-8761c380cd7d'));
        $recipeResult5 = new RecipeResult();
        $recipeResult5->setName('mno');

        $query = new Query();
        $query->setCombinationId($combinationId);

        $expectedRecipeNames = ['abc', 'mno'];

        $recipe1 = $this->createMock(RecipeData::class);
        $recipe2 = $this->createMock(RecipeData::class);
        $newRecipeResult1 = $this->createMock(RecipeResult::class);
        $newRecipeResult2 = $this->createMock(RecipeResult::class);

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->any())
                      ->method('getRecipes')
                      ->willReturn([$recipeResult1, $recipeResult2, $recipeResult3, $recipeResult4, $recipeResult5]);
        $searchResults->expects($this->exactly(2))
                      ->method('addRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($newRecipeResult1)],
                          [$this->identicalTo($newRecipeResult2)],
                      );

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByNames')
                               ->with($this->identicalTo($combinationId), $this->equalTo($expectedRecipeNames))
                               ->willReturn([$recipe1, $recipe2]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($recipe1), $this->isInstanceOf(RecipeResult::class)],
                                [$this->identicalTo($recipe2), $this->isInstanceOf(RecipeResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $newRecipeResult1,
                                $newRecipeResult2,
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);
    }
}
