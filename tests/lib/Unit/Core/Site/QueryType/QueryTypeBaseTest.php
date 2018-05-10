<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

/**
 * Base QueryType test case.
 */
abstract class QueryTypeBaseTest extends TestCase
{
    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType
     */
    abstract protected function getQueryTypeUnderTest();

    /**
     * @return string
     */
    abstract protected function getQueryTypeName();

    public function testGetName()
    {
        $queryType = $this->getQueryTypeUnderTest();

        $this->assertEquals(
            $this->getQueryTypeName(),
            $queryType::getName()
        );
    }

    /**
     * @return string[]
     */
    abstract protected function getSupportedParameters();

    public function testGetSupportedParameters()
    {
        $queryType = $this->getQueryTypeUnderTest();

        $this->assertEquals(
            $this->getSupportedParameters(),
            $queryType->getSupportedParameters()
        );
    }

    public function testSupportsParameterReturnsTrue()
    {
        $queryType = $this->getQueryTypeUnderTest();

        foreach ($this->getSupportedParameters() as $parameter) {
            $this->assertTrue($queryType->supportsParameter($parameter));
        }
    }

    public function testSupportsParameterReturnsFalse()
    {
        $queryType = $this->getQueryTypeUnderTest();

        $this->assertFalse($queryType->supportsParameter(md5(time())));
    }

    public function testGetBaseSupportedParameters()
    {
        $queryType = $this->getQueryTypeUnderTest();
        $parameters = $queryType->getSupportedParameters();

        $expectedParameters = [
            'content_type',
            'fields',
            'publication_date',
            'sort',
            'limit',
            'offset',
        ];

        $this->assertGreaterThanOrEqual(count($expectedParameters), count($parameters));
        $parameterSet = array_flip($parameters);

        foreach ($expectedParameters as $expectedParameter) {
            $this->assertTrue(array_key_exists($expectedParameter, $parameterSet));
            $this->assertTrue($queryType->supportsParameter($expectedParameter));
        }
    }

    abstract public function providerForTestGetQuery();

    /**
     * @dataProvider providerForTestGetQuery
     *
     * @param array $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Query $expectedQuery
     */
    public function testGetQuery(array $parameters, Query $expectedQuery)
    {
        $queryType = $this->getQueryTypeUnderTest();

        $query = $queryType->getQuery($parameters);

        $this->assertEquals(
            $expectedQuery,
            $query
        );
    }

    abstract public function providerForTestGetQueryWithInvalidOptions();

    /**
     * @dataProvider providerForTestGetQueryWithInvalidOptions
     *
     * @param array $parameters
     */
    public function testGetQueryWithInvalidOptions(array $parameters)
    {
        $this->expectException(ExceptionInterface::class);

        $queryType = $this->getQueryTypeUnderTest();

        $queryType->getQuery($parameters);
    }

    abstract public function providerForTestGetQueryWithInvalidCriteria();

    /**
     * @dataProvider providerForTestGetQueryWithInvalidCriteria
     *
     * @param array $parameters
     */
    public function testGetQueryWithInvalidCriteria(array $parameters)
    {
        $this->expectException(InvalidArgumentException::class);

        $queryType = $this->getQueryTypeUnderTest();

        $queryType->getQuery($parameters);
    }

    abstract public function providerForTestInvalidSortClauseThrowsException();

    /**
     * @dataProvider providerForTestInvalidSortClauseThrowsException
     *
     * @param array $parameters
     */
    public function testInvalidSortClauseThrowsException(array $parameters)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp(
            "/Sort string '.*' was not converted to a SortClause/"
        );

        $queryType = $this->getQueryTypeUnderTest();

        $queryType->getQuery($parameters);
    }
}
