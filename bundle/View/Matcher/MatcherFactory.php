<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Matcher;

use eZ\Bundle\EzPublishCoreBundle\Matcher\ViewMatcherRegistry;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Matcher\ClassNameMatcherFactory;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use function mb_strpos;
use function mb_substr;

class MatcherFactory extends ClassNameMatcherFactory
{
    use ContainerAwareTrait;

    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\Matcher\ViewMatcherRegistry|null
     */
    private $viewMatcherRegistry;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var string|null
     */
    private $scope;

    public function __construct(
        ?ViewMatcherRegistry $viewMatcherRegistry,
        Repository $repository,
        string $relativeNamespace,
        ConfigResolverInterface $configResolver,
        string $parameterName,
        ?string $namespace = null,
        ?string $scope = null
    ) {
        $this->viewMatcherRegistry = $viewMatcherRegistry;
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

    /**
     * @param string $matcherIdentifier
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface
     */
    protected function getMatcher($matcherIdentifier)
    {
        if ($this->viewMatcherRegistry !== null && mb_strpos($matcherIdentifier, '@') === 0) {
            return $this->viewMatcherRegistry->getMatcher(mb_substr($matcherIdentifier, 1));
        }

        if ($this->container->has($matcherIdentifier)) {
            /** @var \eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface $matcher */
            $matcher = $this->container->get($matcherIdentifier);

            return $matcher;
        }

        return parent::getMatcher($matcherIdentifier);
    }
}
