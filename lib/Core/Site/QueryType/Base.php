<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use Closure;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base implementation for QueryTypes.
 *
 * @internal do not extend this class directly, extend abstract Content and Location query types instead
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
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinitionResolver
     */
    private $criterionDefinitionResolver;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\QueryType\SortClauseParser
     */
    private $sortClauseParser;

    /**
     * @var \Closure[]
     */
    private $registeredCriterionBuilders;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    final public function getQuery(array $parameters = []): Query
    {
        $parameters = $this->getOptionsResolver()->resolve($parameters);
        $query = $this->buildQuery();

        $sortDefinitions = $parameters['sort'];
        if (!\is_array($sortDefinitions)) {
            $sortDefinitions = [$sortDefinitions];
        }

        $query->query = $this->getQueryCriterion($parameters);
        $query->filter = $this->resolveFilterCriteria($parameters);
        $query->facetBuilders = $this->getFacetBuilders($parameters);
        $query->sortClauses = $this->getSortClauses($sortDefinitions);
        $query->limit = $parameters['limit'];
        $query->offset = $parameters['offset'];

        return $query;
    }

    final public function getSupportedParameters(): array
    {
        return $this->getOptionsResolver()->getDefinedOptions();
    }

    final public function supportsParameter(string $name): bool
    {
        return $this->getOptionsResolver()->isDefined($name);
    }

    /**
     * Configure options with the given options $resolver.
     *
     * Override this method as needed.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        // do nothing
    }

    /**
     * Return filter criteria.
     *
     * Here you can return null, a single criterion or an array of criteria.
     * If an array of criteria is returned, it will be combined with base criteria
     * using logical AND.
     * Override this method as needed.
     *
     * @param array $parameters
     *
     * @return null|Criterion|Criterion[]
     */
    protected function getFilterCriteria(array $parameters)
    {
        return null;
    }

    /**
     * Return query Criterion.
     *
     * Here you can return null or a Criterion instance.
     * Override this method as needed.
     *
     * @param array $parameters
     *
     * @return null|Criterion
     */
    protected function getQueryCriterion(array $parameters): ?Criterion
    {
        return null;
    }

    /**
     * Return an array of FacetBuilder instances.
     *
     * Note: facets are supported only with Solr search engine, which will be available
     * through FindService. By default query types use FilterService, where faceting is
     * not supported. You can control that behavior with 'use_filter' option of the query
     * configuration (defaulting to false).
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService
     * @see \Netgen\EzPlatformSiteApi\API\FindService
     *
     * Return an empty array if you don't need to use facets.
     * Override this method as needed.
     *
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder[]
     */
    protected function getFacetBuilders(array $parameters): array
    {
        return [];
    }

    /**
     * Register criterion builders using registerCriterionBuilder().
     *
     * Override this method as needed.
     *
     * @see registerCriterionBuilder()
     */
    protected function registerCriterionBuilders(): void
    {
        // do nothing
    }

    /**
     * Parse custom sort string.
     *
     * Override the method if needed, this implementation will only throw an exception.
     *
     * @param string $string
     *
     * @return null|SortClause
     */
    protected function parseCustomSortString(string $string): ?SortClause
    {
        throw new InvalidArgumentException(
            "Sort string '{$string}' was not converted to a SortClause"
        );
    }

    /**
     * Register builder closure for $name Criterion.
     *
     * Closure will be called with an instance of CriterionDefinition and an array of QueryType
     * parameters and it must return a Criterion instance.
     *
     * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition
     *
     * @param string $name
     * @param \Closure $builder
     */
    final protected function registerCriterionBuilder(string $name, Closure $builder): void
    {
        $this->registeredCriterionBuilders[$name] = $builder;
    }

    /**
     * Return the appropriate Query instance.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query
     */
    abstract protected function buildQuery(): Query;

    /**
     * Configure $resolver for the QueryType.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configureBaseOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'content_type',
            'field',
            'is_field_empty',
            'creation_date',
            'modification_date',
            'section',
            'state',
        ]);
        $resolver->setDefaults([
            'sort' => [],
            'limit' => 25,
            'offset' => 0,
        ]);

        $resolver->setAllowedTypes('content_type', ['null', 'string', 'array']);
        $resolver->setAllowedTypes('section', ['null', 'string', 'array']);
        $resolver->setAllowedTypes('field', ['array']);
        $resolver->setAllowedTypes('is_field_empty', ['null', 'array']);
        $resolver->setAllowedTypes('limit', ['null', 'int']);
        $resolver->setAllowedTypes('offset', ['null', 'int']);
        $resolver->setAllowedTypes('creation_date', ['int', 'string', 'array']);
        $resolver->setAllowedTypes('modification_date', ['int', 'string', 'array']);
        $resolver->setAllowedTypes('state', ['array']);

        $resolver->setNormalizer('limit', static function (Options $options, $value) {return $value ?? 25;});
        $resolver->setNormalizer('offset', static function (Options $options, $value) {return $value ?? 0;});
        $resolver->setNormalizer('is_field_empty', static function (Options $options, $value) {return $value ?? [];});

        $resolver->setAllowedValues(
            'is_field_empty',
            static function ($isEmptyMap): bool {
                if ($isEmptyMap === null) {
                    return true;
                }

                foreach ($isEmptyMap as $key => $value) {
                    if (!\is_string($key) || ($value !== null && !\is_bool($value))) {
                        return false;
                    }
                }

                return true;
            }
        );

        $resolver->setAllowedTypes('sort', ['string', SortClause::class, APILocation::class, 'array']);

        $resolver->setNormalizer(
            'sort',
            static function (Options $options, $value) {
                if ($value instanceof APILocation) {
                    return $value->innerLocation->getSortClauses();
                }

                return $value ?? [];
            }
        );
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
    private function buildBaseCriteria(array $parameters): array
    {
        $criteriaGrouped = [[]];

        foreach ($parameters as $name => $value) {
            $definitions = $this->resolveCriterionDefinitions($name, $value);

            if (!empty($definitions)) {
                $criteriaGrouped[] = $this->getCriteriaBuilder()->build($definitions);
            }
        }

        return \array_merge(...$criteriaGrouped);
    }

    /**
     * @param string $name
     * @param mixed $parameters
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[]
     */
    private function resolveCriterionDefinitions(string $name, $parameters): array
    {
        switch ($name) {
            case 'content_type':
            case 'depth':
            case 'main':
            case 'parent_location_id':
            case 'priority':
            case 'publication_date':
            case 'creation_date':
            case 'modification_date':
            case 'section':
            case 'subtree':
            case 'visible':
                return $this->getCriterionDefinitionResolver()->resolve($name, $parameters);
            case 'field':
            case 'state':
            case 'is_field_empty':
                return $this->getCriterionDefinitionResolver()->resolveTargets($name, $parameters);
        }

        return [];
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    private function buildRegisteredCriteria(array $parameters): array
    {
        if ($this->registeredCriterionBuilders === null) {
            $this->registeredCriterionBuilders = [];
            $this->registerCriterionBuilders();
        }

        $criteriaGrouped = [[]];

        foreach ($this->registeredCriterionBuilders as $name => $builder) {
            $criteriaGrouped[] = $this->buildCriteria($builder, $name, $parameters);
        }

        return \array_merge(...$criteriaGrouped);
    }

    /**
     * @param \Closure $builder
     * @param string $name
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    private function buildCriteria(Closure $builder, string $name, array $parameters): array
    {
        $criteria = [];

        if (\array_key_exists($name, $parameters)) {
            $definitions = $this->getCriterionDefinitionResolver()->resolve($name, $parameters[$name]);

            foreach ($definitions as $definition) {
                $builtCriteria = $builder($definition, $parameters);

                if ($builtCriteria === null) {
                    continue;
                }

                $criteria[] = $builtCriteria;
            }
        }

        return $criteria;
    }

    /**
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return null|\eZ\Publish\API\Repository\Values\Content\Query\Criterion
     */
    private function resolveFilterCriteria(array $parameters): ?Criterion
    {
        $baseCriteria = $this->buildBaseCriteria($parameters);
        $registeredCriteria = $this->buildRegisteredCriteria($parameters);
        $filterCriteria = $this->getFilterCriteria($parameters);

        if ($filterCriteria === null) {
            $filterCriteria = [];
        }

        if ($filterCriteria instanceof Criterion) {
            $filterCriteria = [$filterCriteria];
        }

        $criteria = \array_merge($baseCriteria, $registeredCriteria, $filterCriteria);

        if (empty($criteria)) {
            return null;
        }

        if (\count($criteria) === 1) {
            return $criteria[0];
        }

        return new LogicalAnd($criteria);
    }

    /**
     * Return an array of SortClause instances from the given $parameters.
     *
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(array $parameters): array
    {
        $sortClauses = [];

        foreach ($parameters as $parameter) {
            if (\is_string($parameter)) {
                $parameter = $this->parseSortString($parameter);
            }

            if (\is_string($parameter)) {
                $parameter = $this->parseCustomSortString($parameter);
            }

            $sortClauses[] = $parameter;
        }

        return $sortClauses;
    }

    /**
     * @param string $string
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause|string
     */
    private function parseSortString(string $string)
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
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private function getOptionsResolver(): OptionsResolver
    {
        if ($this->optionsResolver === null) {
            $this->optionsResolver = new OptionsResolver();
            $this->configureBaseOptions($this->optionsResolver);
            $this->configureOptions($this->optionsResolver);
        }

        return $this->optionsResolver;
    }

    private function getCriterionDefinitionResolver(): CriterionDefinitionResolver
    {
        if ($this->criterionDefinitionResolver === null) {
            $this->criterionDefinitionResolver = new CriterionDefinitionResolver();
        }

        return $this->criterionDefinitionResolver;
    }

    private function getCriteriaBuilder(): CriteriaBuilder
    {
        if ($this->criteriaBuilder === null) {
            $this->criteriaBuilder = new CriteriaBuilder();
        }

        return $this->criteriaBuilder;
    }

    private function getSortClauseParser(): SortClauseParser
    {
        if ($this->sortClauseParser === null) {
            $this->sortClauseParser = new SortClauseParser();
        }

        return $this->sortClauseParser;
    }
}
