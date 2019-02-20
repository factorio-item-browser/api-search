<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Parser;

use BluePsyduck\Common\Test\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the QueryParser class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Search\Parser\QueryParser
 */
class QueryParserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the parse method.
     * @throws ReflectionException
     * @covers ::parse
     */
    public function testParse(): void
    {
        $queryString = 'abc';
        $modCombinationIds = [42, 1337];
        $locale = 'def';
        $hash = '12ab34cd';

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('setHash')
              ->with($this->identicalTo($hash));

        /* @var QueryParser&MockObject $parser */
        $parser = $this->getMockBuilder(QueryParser::class)
                       ->setMethods(['createQuery', 'parseQueryString', 'calculateHash'])
                       ->getMock();
        $parser->expects($this->once())
               ->method('createQuery')
               ->with(
                   $this->identicalTo($queryString),
                   $this->identicalTo($modCombinationIds),
                   $this->identicalTo($locale)
               )
               ->willReturn($query);
        $parser->expects($this->once())
               ->method('parseQueryString')
               ->with($this->identicalTo($queryString), $this->identicalTo($query));
        $parser->expects($this->once())
               ->method('calculateHash')
               ->with($this->identicalTo($query))
               ->willReturn($hash);

        $result = $parser->parse($queryString, $modCombinationIds, $locale);

        $this->assertSame($query, $result);
    }

    /**
     * Tests the createQuery method.
     * @throws ReflectionException
     * @covers ::createQuery
     */
    public function testCreateQuery(): void
    {
        $queryString = 'abc';
        $modCombinationIds = [42, 1337];
        $locale = 'def';
        $expectedResult = new Query($queryString, $modCombinationIds, $locale);

        $parser = new QueryParser();
        $result = $this->invokeMethod($parser, 'createQuery', $queryString, $modCombinationIds, $locale);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Provides the data for the parseQueryString test.
     * @return array
     */
    public function provideParseQueryString(): array
    {
        return [
            ['abc', ['abc']],
            ['abc def', ['abc', 'def']],
            ['abc  def', ['abc', 'def']],
            ['abc d efg', ['abc', 'efg']],
        ];
    }

    /**
     * Tests the parseQueryString method.
     * @param string $queryString
     * @param array|string[] $expectedKeywords
     * @throws ReflectionException
     * @covers ::parseQueryString
     * @dataProvider provideParseQueryString
     */
    public function testParseQueryString(string $queryString, array $expectedKeywords): void
    {
        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        /* @var QueryParser&MockObject $parser */
        $parser = $this->getMockBuilder(QueryParser::class)
                       ->setMethods(['createTerm'])
                       ->getMock();

        foreach ($expectedKeywords as $index => $expectedKeyword) {
            /* @var Term&MockObject $term */
            $term = $this->createMock(Term::class);

            $parser->expects($this->at($index))
                   ->method('createTerm')
                   ->with($this->identicalTo(TermType::GENERIC), $this->identicalTo($expectedKeyword))
                   ->willReturn($term);

            $query->expects($this->at($index))
                  ->method('addTerm')
                  ->with($this->identicalTo($term));
        }

        $this->invokeMethod($parser, 'parseQueryString', $queryString, $query);
    }

    /**
     * Tests the createTerm method.
     * @throws ReflectionException
     * @covers ::createTerm
     */
    public function testCreateTerm(): void
    {
        $type = 'abc';
        $value = 'def';
        $expectedResult = new Term('abc', 'def');

        $parser = new QueryParser();
        $result = $this->invokeMethod($parser, 'createTerm', $type, $value);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the calculateHash method.
     * @throws ReflectionException
     * @covers ::calculateHash
     */
    public function testCalculateHash(): void
    {
        $queryData = ['abc' => 'def'];
        $modCombinationIds = [42, 1337];
        $locale = 'ghi';
        $expectedResult = 'd72197e632fa2195';

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getModCombinationIds')
              ->willReturn($modCombinationIds);
        $query->expects($this->once())
              ->method('getLocale')
              ->willReturn($locale);

        /* @var QueryParser&MockObject $parser */
        $parser = $this->getMockBuilder(QueryParser::class)
                       ->setMethods(['extractQueryData'])
                       ->getMock();
        $parser->expects($this->once())
               ->method('extractQueryData')
               ->with($this->identicalTo($query))
               ->willReturn($queryData);

        $result = $this->invokeMethod($parser, 'calculateHash', $query);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the extractQueryData method.
     * @throws ReflectionException
     * @covers ::extractQueryData
     */
    public function testExtractQueryData(): void
    {
        $term1 = new Term('ghi', 'jkl');
        $term2 = new Term('abc', 'def');

        $expectedResult = ['abc|def', 'ghi|jkl'];

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('getTerms')
              ->willReturn([$term1, $term2]);

        $parser = new QueryParser();
        $result = $this->invokeMethod($parser, 'extractQueryData', $query);

        $this->assertEquals($expectedResult, $result);
    }
}
