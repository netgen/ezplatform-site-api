<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionCollection;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class QueryDefinitionCollectionTest extends TestCase
{
    public function testAddAndGetQueryDefinition(): void
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

    public function testGetQueryDefinitionThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $queryDefinitionCollection = $this->getQueryDefinitionCollectionUnderTest();

        $queryDefinitionCollection->get('jerry');
    }

    protected function getQueryDefinitionCollectionUnderTest(): QueryDefinitionCollection
    {
        return new QueryDefinitionCollection();
    }
}
