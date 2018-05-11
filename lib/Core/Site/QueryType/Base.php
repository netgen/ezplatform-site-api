<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base implementation for QueryTypes.
 *
 * @internal Do not extend this class directly, extend abstract Content and Location
 * query types instead.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
abstract class Base implements QueryType
{
    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgumentResolver
     */
    private $criterionArgumentResolver;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\SortClauseParser
     */
    private $sortClauseParser;

    /**
     * Configure options with the given options $resolver if needed.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    abstract protected function configureOptions(OptionsResolver $resolver);

    /**
     * Return filter criteria.
     *
     * Here you can return null, a single criterion or an array of criteria.
     * If an array of criteria is returned, it will be combined with base criteria
     * using logical AND.
     *
     * @param array $parameters
     *
     * @return null|Criterion|Criterion[]
     */
    abstract protected function getFilterCriteria(array $parameters);

    /**
     * Return query criteria.
     *
     * Here you can return null or a Criterion instance.
     *
     * @param array $parameters
     *
     * @return null|Criterion
     */
    abstract protected function getQueryCriteria(array $parameters);

    /**
     * Return an array of FacetBuilder instances.
     *
     * Return an empty array if you don't need to use facets.
     *
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder[]
     */
    abstract protected function getFacetBuilders(array $parameters);

    /**
     * Parse custom sort string.
     *
     * Override the method if needed, this implementation will only throw an exception.
     *
     * @param string $string
     *
     * @return mixed
     */
    protected function parseCustomSortString($string)
    {
        throw new InvalidArgumentException(
            "Sort string '{$string}' was not converted to a SortClause"
        );
    }

    /**
     * Resolve criterion configuration to CriterionArgument instance.
     *
     * @param mixed $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument[]
     */
    protected function resolveCriterionArguments($parameters)
    {
        return $this->getCriterionArgumentResolver()->resolve($parameters);
    }

    /**
     * Return the appropriate query instance.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query
     */
    abstract protected function buildQuery();

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface|\InvalidArgumentException
     * @throws \RuntimeException
     */
    final public function getQuery(array $parameters = [])
    {
        $parameters = $this->getOptionsResolver()->resolve($parameters);
        $query = $this->buildQuery();

        $sortDefinitions = $parameters['sort'];
        if (!is_array($sortDefinitions)) {
            $sortDefinitions = [$sortDefinitions];
        }

        $query->query = $this->getQueryCriteria($parameters);
        $query->filter = $this->resolveFilterCriteria($parameters);
        $query->facetBuilders = $this->getFacetBuilders($parameters);
        $query->sortClauses = $this->getSortClauses($sortDefinitions);
        $query->limit = $parameters['limit'];
        $query->offset = $parameters['offset'];

        return $query;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     */
    final public function getSupportedParameters()
    {
        return $this->getOptionsResolver()->getDefinedOptions();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     */
    final public function supportsParameter($name)
    {
        return $this->getOptionsResolver()->isDefined($name);
    }

    /**
     * Configure $resolver for the QueryType.
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configureBaseOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined([
            'content_type',
            'field',
            'publication_date',
        ]);
        $resolver->setDefaults([
            'sort' => [],
            'limit' => 25,
            'offset' => 0,
        ]);

        $resolver->setAllowedTypes('content_type', ['string', 'string[]']);
        $resolver->setAllowedTypes('field', ['array']);
        $resolver->setAllowedTypes('limit', ['int']);
        $resolver->setAllowedTypes('offset', ['int']);
        $resolver->setAllowedTypes('publication_date', ['int', 'string', 'array']);

        $class = SortClause::class;
        $resolver->setAllowedTypes('sort', ['string', $class, 'array']);
    }

    /**
     * Build criteria for the base supported options.
     *
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    private function buildBaseCriteria(array $parameters)
    {
        $criteriaGrouped = [[]];

        foreach ($parameters as $name => $value) {
            switch ($name) {
                case 'content_type':
                case 'depth':
                case 'main':
                case 'parent_location_id':
                case 'priority':
                case 'publication_date':
                case 'subtree':
                case 'visible':
                    $arguments = $this->getCriterionArgumentResolver()->resolve($value);
                    break;
                case 'field':
                    $arguments = $this->getCriterionArgumentResolver()->resolveTargets($value);
                    break;
                default:
                    continue 2;
            }

            $criteriaGrouped[] = $this->getCriteriaBuilder()->build($name, $arguments);
        }

        return array_merge(...$criteriaGrouped);
    }

    /**
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function resolveFilterCriteria(array $parameters)
    {
        $baseCriteria = $this->buildBaseCriteria($parameters);
        $filterCriteria = $this->getFilterCriteria($parameters);

        if (null === $filterCriteria) {
            $filterCriteria = [];
        }

        if ($filterCriteria instanceof Criterion) {
            $filterCriteria = [$filterCriteria];
        }

        $criteria = array_merge($baseCriteria, $filterCriteria);

        if (empty($criteria)) {
            return null;
        }

        if (1 === count($criteria)) {
            return $criteria[0];
        }

        return new LogicalAnd($criteria);
    }

    /**
     * Return an array of SortClause instances from the given $parameters.
     *
     * @throws \InvalidArgumentException
     *
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(array $parameters)
    {
        $sortClauses = [];

        foreach ($parameters as $parameter) {
            if (is_string($parameter)) {
                $parameter = $this->parseSortString($parameter);
            }

            if (is_string($parameter)) {
                $parameter = $this->parseCustomSortString($parameter);
            }

            $sortClauses[] = $parameter;
        }

        return $sortClauses;
    }

    /**
     * @param string|\eZ\Publish\API\Repository\Values\Content\Query\SortClause $string
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause
     */
    private function parseSortString($string)
    {
        try {
            return $this->getSortClauseParser()->parse($string);
        } catch (InvalidArgumentException $e) {
            // do nothing
        }

        return $string;
    }

    /**
     * Builds the resolver and configures it using configureOptions().
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private function getOptionsResolver()
    {
        if ($this->optionsResolver === null) {
            $this->optionsResolver = new OptionsResolver();
            $this->configureBaseOptions($this->optionsResolver);
            $this->configureOptions($this->optionsResolver);
        }

        return $this->optionsResolver;
    }

    private function getCriterionArgumentResolver()
    {
        if ($this->criterionArgumentResolver === null) {
            $this->criterionArgumentResolver = new CriterionArgumentResolver();
        }

        return $this->criterionArgumentResolver;
    }

    private function getCriteriaBuilder()
    {
        if ($this->criteriaBuilder === null) {
            $this->criteriaBuilder = new CriteriaBuilder();
        }

        return $this->criteriaBuilder;
    }

    private function getSortClauseParser()
    {
        if ($this->sortClauseParser === null) {
            $this->sortClauseParser = new SortClauseParser();
        }

        return $this->sortClauseParser;
    }
}
