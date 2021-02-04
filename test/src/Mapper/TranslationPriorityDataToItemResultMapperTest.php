<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Mapper;

use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToItemResultMapper;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TranslationPriorityDataToItemResultMapper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Mapper\TranslationPriorityDataToItemResultMapper
 */
class TranslationPriorityDataToItemResultMapperTest extends TestCase
{
    public function testMeta(): void
    {
        $instance = new TranslationPriorityDataToItemResultMapper();

        $this->assertSame(TranslationPriorityData::class, $instance->getSupportedSourceClass());
        $this->assertSame(ItemResult::class, $instance->getSupportedDestinationClass());
    }

    public function testMap(): void
    {
        $source = new TranslationPriorityData();
        $source->setType('abc')
               ->setName('def')
               ->setPriority(42);

        $expectedDestination = new ItemResult();
        $expectedDestination->setType('abc')
                            ->setName('def')
                            ->setPriority(42);

        $destination = new ItemResult();

        $instance = new TranslationPriorityDataToItemResultMapper();
        $instance->map($source, $destination);

        $this->assertEquals($expectedDestination, $destination);
    }
}
