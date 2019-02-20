<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Fetcher;

use BluePsyduck\Common\Test\ReflectionTrait;
use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Filter\DataFilter;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the RecipeFetcher class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Fetcher\RecipeFetcher
 */
class RecipeFetcherTest extends TestCase
{
    use ReflectionTrait;
    
    /**
     * The mocked data filter.
     * @var DataFilter&MockObject
     */
    protected $dataFilter;
    
    /**
     * The mocked mapper manager.
     * @var MapperManagerInterface&MockObject
     */
    protected $mapperManager;
    
    /**
     * The mocked recipe repository.
     * @var RecipeRepository&MockObject
     */
    protected $recipeRepository;

    /**
     * Sets up the test case.
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dataFilter = $this->createMock(DataFilter::class);
        $this->mapperManager = $this->createMock(MapperManagerInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
    }

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $fetcher = new RecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        
        $this->assertSame($this->dataFilter, $this->extractProperty($fetcher, 'dataFilter'));
        $this->assertSame($this->mapperManager, $this->extractProperty($fetcher, 'mapperManager'));
        $this->assertSame($this->recipeRepository, $this->extractProperty($fetcher, 'recipeRepository'));
    }

    /**
     * Tests the fetch method.
     * @throws MapperException
     * @throws ReflectionException
     * @covers ::fetch
     */
    public function testFetch(): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /* @var RecipeData&MockObject $recipe1 */
        $recipe1 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe2 */
        $recipe2 = $this->createMock(RecipeData::class);
        /* @var RecipeData&MockObject $recipe3 */
        $recipe3 = $this->createMock(RecipeData::class);
        /* @var RecipeResult&MockObject $recipeResult1 */
        $recipeResult1 = $this->createMock(RecipeResult::class);
        /* @var RecipeResult&MockObject $recipeResult2 */
        $recipeResult2 = $this->createMock(RecipeResult::class);

        $recipes = [$recipe1, $recipe2, $recipe3];
        $filteredRecipes = [$recipe1, $recipe2];

        /* @var AggregatingResultCollection&MockObject $searchResults */
        $searchResults = $this->createMock(AggregatingResultCollection::class);
        $searchResults->expects($this->exactly(2))
                      ->method('addRecipe')
                      ->withConsecutive(
                          [$this->identicalTo($recipeResult1)],
                          [$this->identicalTo($recipeResult2)]
                      );

        $this->dataFilter->expects($this->once())
                         ->method('filter')
                         ->with($this->identicalTo($recipes))
                         ->willReturn($filteredRecipes);


        /* @var RecipeFetcher&MockObject $fetcher */
        $fetcher = $this->getMockBuilder(RecipeFetcher::class)
                        ->setMethods(['fetchRecipes', 'mapRecipeData'])
                        ->setConstructorArgs([$this->dataFilter, $this->mapperManager, $this->recipeRepository])
                        ->getMock();
        $fetcher->expects($this->once())
                ->method('fetchRecipes')
                ->with($this->identicalTo($query))
                ->willReturn($recipes);
        $fetcher->expects($this->exactly(2))
                ->method('mapRecipeData')
                ->withConsecutive(
                    [$this->identicalTo($recipe1)],
                    [$this->identicalTo($recipe2)]
                )
                ->willReturnOnConsecutiveCalls(
                    $recipeResult1,
                    $recipeResult2
                );

        $fetcher->fetch($query, $searchResults);
    }

    /**
     * Tests the fetchRecipes method.
     * @throws ReflectionException
     * @covers ::fetchRecipes
     */
    public function testFetchRecipes(): void
    {
        $keywords = ['abc', 'def'];
        $modCombinationIds = [42, 1337];

        $recipes = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getTermValuesByType')
              ->with($this->identicalTo(TermType::GENERIC))
              ->willReturn($keywords);
        $query->expects($this->once())
              ->method($this->identicalTo('getModCombinationIds'))
              ->willReturn($modCombinationIds);

        $this->recipeRepository->expects($this->once())
                               ->method('findDataByKeywords')
                               ->with($this->identicalTo($keywords), $this->identicalTo($modCombinationIds))
                               ->willReturn($recipes);

        $fetcher = new RecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'fetchRecipes', $query);

        $this->assertSame($recipes, $result);
    }
    
    /**
     * Tests the mapRecipeData method.
     * @throws ReflectionException
     * @covers ::mapRecipeData
     */
    public function testMapRecipeData(): void
    {
        /* @var RecipeData&MockObject $recipe */
        $recipe = $this->createMock(RecipeData::class);

        $this->mapperManager->expects($this->once())
                            ->method('map')
                            ->with($this->identicalTo($recipe), $this->isInstanceOf(RecipeResult::class));

        $fetcher = new RecipeFetcher($this->dataFilter, $this->mapperManager, $this->recipeRepository);
        $result = $this->invokeMethod($fetcher, 'mapRecipeData', $recipe);

        $this->assertSame(SearchResultPriority::EXACT_MATCH, $result->getPriority());
    }
}
