<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToRecipeResultMapper;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TranslationPriorityDataToRecipeResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToRecipeResultMapper
 */
class TranslationPriorityDataToRecipeResultMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new TranslationPriorityDataToRecipeResultMapper();

        $this->assertSame(TranslationPriorityData::class, $instance->getSupportedSourceClass());
        $this->assertSame(RecipeResult::class, $instance->getSupportedDestinationClass());
    }

    public function testMap(): void
    {
        $source = new TranslationPriorityData();
        $source->setName('def')
               ->setPriority(42);

        $expectedDestination = new RecipeResult();
        $expectedDestination->setName('def')
                            ->setPriority(42);

        $destination = new RecipeResult();

        $instance = new TranslationPriorityDataToRecipeResultMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
