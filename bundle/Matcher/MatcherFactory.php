<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Matcher;

use eZ\Bundle\EzPublishCoreBundle\Matcher\ServiceAwareMatcherFactory;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;

class MatcherFactory extends ServiceAwareMatcherFactory
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var null|string
     */
    private $namespace;

    /**
     * @var null|string
     */
    private $scope;

    public function __construct(
        Repository $repository,
        string $relativeNamespace,
        ConfigResolverInterface $configResolver,
        string $parameterName,
        ?string $namespace = null,
        ?string $scope = null
    ) {
        $this->configResolver = $configResolver;
        $this->parameterName = $parameterName;
        $this->namespace = $namespace;
        $this->scope = $scope;

        parent::__construct($repository, $relativeNamespace);
    }

    public function match(View $view): ?array
    {
        $matchConfig = $this->configResolver->getParameter($this->parameterName, $this->namespace, $this->scope);
        $this->setMatchConfig($matchConfig);

        return parent::match($view);
    }
}
