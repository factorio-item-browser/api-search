<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Parser;

use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the QueryParser class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Search\Parser\QueryParser
 */
class QueryParserTest extends TestCase
{
    public function testParse(): void
    {
        $locale = 'foo';
        $queryString = 'abc z Ghi  dEf ';
        $expectedTerms = ['abc', 'ghi', 'def'];
        $expectedHash = '62c5cb05-3a10-998d-9a7e-76d0815ece9b';

        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $instance = new QueryParser();
        $result = $instance->parse($combinationId, $locale, $queryString);

        $this->assertSame($combinationId, $result->getCombinationId());
        $this->assertSame($locale, $result->getLocale());
        $this->assertSame($queryString, $result->getQueryString());
        $this->assertSame($expectedHash, $result->getHash()->toString());
        $this->assertEquals($expectedTerms, $result->getTerms()->getAllValues());
    }
}
