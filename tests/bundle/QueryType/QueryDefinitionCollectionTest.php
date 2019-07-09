<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionCollection;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class QueryDefinitionCollectionTest extends TestCase
{
    public function testAddAndGetQueryDefinition()
    {
        $queryDefinitionCollection = $this->getQueryDefinitionCollectionUnderTest();
        $queryDefinition = new QueryDefinition();
        $name = 'test';

        $queryDefinitionCollection->add($name, $queryDefinition);

        $this->assertSame(
            $queryDefinition,
            $queryDefinitionCollection->get($name)
        );
    }

    public function testGetQueryDefinitionThrowsException()
    {
        $this->expectException(OutOfBoundsException::class);

        $queryDefinitionCollection = $this->getQueryDefinitionCollectionUnderTest();

        $queryDefinitionCollection->get('jerry');
    }

    protected function getQueryDefinitionCollectionUnderTest()
    {
        return new QueryDefinitionCollection();
    }
}
