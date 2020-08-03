<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Search\Parser;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Term;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
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
     * @covers ::parse
     */
    public function testParse(): void
    {
        $locale = 'def';
        $queryString = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $hash */
        $hash = $this->createMock(UuidInterface::class);

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
              ->method('setHash')
              ->with($this->identicalTo($hash));

        /* @var QueryParser&MockObject $parser */
        $parser = $this->getMockBuilder(QueryParser::class)
                       ->onlyMethods(['createQuery', 'parseQueryString', 'calculateHash'])
                       ->getMock();
        $parser->expects($this->once())
               ->method('createQuery')
               ->with(
                   $this->identicalTo($combinationId),
                   $this->identicalTo($locale),
                   $this->identicalTo($queryString)
               )
               ->willReturn($query);
        $parser->expects($this->once())
               ->method('parseQueryString')
               ->with($this->identicalTo($queryString), $this->identicalTo($query));
        $parser->expects($this->once())
               ->method('calculateHash')
               ->with($this->identicalTo($query))
               ->willReturn($hash);

        $result = $parser->parse($combinationId, $locale, $queryString);

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
        $locale = 'def';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $expectedResult = new Query();
        $expectedResult->setCombinationId($combinationId)
                       ->setLocale('def')
                       ->setQueryString('abc');

        $parser = new QueryParser();
        $result = $this->invokeMethod($parser, 'createQuery', $combinationId, $locale, $queryString);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Provides the data for the parseQueryString test.
     * @return array<mixed>
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
                       ->onlyMethods(['createTerm'])
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
        $queryData = ['abc', 'def'];
        $expectedHash = '9e86daa1-e1bd-94ed-176d-afd437e13d58';

        /* @var Query&MockObject $query */
        $query = $this->createMock(Query::class);

        /* @var QueryParser&MockObject $parser */
        $parser = $this->getMockBuilder(QueryParser::class)
                       ->onlyMethods(['extractQueryData'])
                       ->getMock();
        $parser->expects($this->once())
               ->method('extractQueryData')
               ->with($this->identicalTo($query))
               ->willReturn($queryData);

        /* @var UuidInterface $result */
        $result = $this->invokeMethod($parser, 'calculateHash', $query);

        $this->assertEquals($expectedHash, $result->toString());
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
