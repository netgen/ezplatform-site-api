<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Base;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\FullText;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\SectionFacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionName;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test stub for custom QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base
 */
class CustomQueryType extends Base
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'modification_date',
        ]);

        $resolver->setAllowedTypes('modification_date', ['int', 'string', 'array']);
    }

    protected function getFilterCriteria(array $parameters)
    {
        $criteria = [];
        $criterionArguments = $this->resolveCriterionDefinitions($parameters['modification_date']);

        foreach ($criterionArguments as $criterionArgument) {
            $criteria[] = new DateMetadata(
                DateMetadata::MODIFIED,
                $criterionArgument->operator,
                $criterionArgument->value
            );
        }

        return $criteria;
    }

    protected function buildQuery()
    {
        return new LocationQuery();
    }

    public static function getName()
    {
        return 'Test:Custom';
    }

    protected function getQueryCriteria(array $parameters)
    {
        return new FullText('one AND two OR three');
    }

    protected function getFacetBuilders(array $parameters)
    {
        return [
            new SectionFacetBuilder(),
        ];
    }

    protected function parseCustomSortString($string)
    {
        switch ($string) {
            case 'section':
                return new SectionIdentifier();
            case 'whatever':
                return new SectionName();
        }

        return null;
    }
}
