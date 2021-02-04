<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\ProductRecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the ProductRecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Fetcher\ProductRecipeFetcher
 */
class ProductRecipeFetcherTest extends TestCase
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
     * @return ProductRecipeFetcher&MockObject
     */
    private function createInstance(array $mockedMethods = []): ProductRecipeFetcher
    {
        return $this->getMockBuilder(ProductRecipeFetcher::class)
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
        $combinationId = $this->createMock(UuidInterface::class);
        $query = new Query();
        $query->setCombinationId($combinationId);

        $id1 = Uuid::fromString('1b467edb-1fea-4f97-b829-ce5f666f2095');
        $id2 = Uuid::fromString('2e061889-89f8-47a6-891a-3ab1ddd64123');
        $id3 = Uuid::fromString('32702df6-eebc-4bc2-be35-315678fa53d7');
        $id4 = Uuid::fromString('462456b5-de9a-4675-ace0-dbe4c960eb01');

        $recipe1 = new RecipeData();
        $recipe1->setItemId($id1);
        $recipe2 = new RecipeData();
        $recipe2->setItemId($id4);
        $recipe3 = new RecipeData();
        $recipe3->setItemId($id3);

        $recipeResult1 = $this->createMock(RecipeResult::class);
        $recipeResult2 = $this->createMock(RecipeResult::class);

        $item1 = new ItemResult();
        $item1->setId($id1);
        $item2 = new ItemResult();
        $item2->setId($id2);
        $item3 = new ItemResult();
        $item3->setId($id3);
        $item4 = new ItemResult();

        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->any())
                      ->method('getItems')
                      ->willReturn([$item1, $item2, $item3, $item4]);

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByProductItemIds')
                               ->with($this->identicalTo($combinationId), $this->equalTo([$id1, $id2, $id3]))
                               ->willReturn([$recipe1, $recipe2, $recipe3]);

        $this->mapperManager->expects($this->exactly(2))
                            ->method('map')
                            ->withConsecutive(
                                [$this->identicalTo($recipe1), $this->isInstanceOf(RecipeResult::class)],
                                [$this->identicalTo($recipe3), $this->isInstanceOf(RecipeResult::class)],
                            )
                            ->willReturnOnConsecutiveCalls(
                                $recipeResult1,
                                $recipeResult2,
                            );

        $instance = $this->createInstance();
        $instance->fetch($query, $searchResults);

        $this->assertEquals($item1->getRecipes(), [$recipeResult1]);
        $this->assertEquals($item3->getRecipes(), [$recipeResult2]);
    }
}
