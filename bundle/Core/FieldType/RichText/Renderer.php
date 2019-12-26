<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Core\FieldType\RichText;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformRichTextBundle\eZ\RichText\Renderer as CoreRenderer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\EngineInterface;

class Renderer extends CoreRenderer
{
    private $ngEmbedConfigurationNamespace;

    public function __construct(
        Repository $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        ConfigResolverInterface $configResolver,
        EngineInterface $templateEngine,
        $tagConfigurationNamespace,
        $styleConfigurationNamespace,
        $embedConfigurationNamespace,
        $ngEmbedConfigurationNamespace,
        LoggerInterface $logger = null,
        array $customTagsConfiguration = [],
        array $customStylesConfiguration = []
    ) {
        parent::__construct(
            $repository,
            $authorizationChecker,
            $configResolver,
            $templateEngine,
            $tagConfigurationNamespace,
            $styleConfigurationNamespace,
            $embedConfigurationNamespace,
            $logger,
            $customTagsConfiguration,
            $customStylesConfiguration
        );

        $this->ngEmbedConfigurationNamespace = $ngEmbedConfigurationNamespace;
    }

    /**
     * Returns configured template reference for the given embed parameters.
     *
     * @param $resourceType
     * @param $isInline
     * @param $isDenied
     *
     * @return null|string
     */
    protected function getEmbedTemplateName($resourceType, $isInline, $isDenied): ?string
    {
        $configurationReference = $this->getConfigurationReference();

        if ($resourceType === static::RESOURCE_TYPE_CONTENT) {
            $configurationReference .= '.content';
        } else {
            $configurationReference .= '.location';
        }

        if ($isInline) {
            $configurationReference .= '_inline';
        }

        if ($isDenied) {
            $configurationReference .= '_denied';
        }

        if ($this->configResolver->hasParameter($configurationReference)) {
            $configuration = $this->configResolver->getParameter($configurationReference);

            return $configuration['template'];
        }

        $this->logger->warning(
            "Embed tag configuration '{$configurationReference}' was not found"
        );

        $configurationReference = $this->getConfigurationReference();

        $configurationReference .= '.default';

        if ($isInline) {
            $configurationReference .= '_inline';
        }

        if ($this->configResolver->hasParameter($configurationReference)) {
            $configuration = $this->configResolver->getParameter($configurationReference);

            return $configuration['template'];
        }

        $this->logger->warning(
            "Embed tag default configuration '{$configurationReference}' was not found"
        );

        return null;
    }

    private function getConfigurationReference(): string
    {
        $overrideViewAction = $this->configResolver->getParameter(
            'override_url_alias_view_action',
            'netgen_ez_platform_site_api'
        );

        if ($overrideViewAction) {
            return $this->ngEmbedConfigurationNamespace;
        }

        return $this->embedConfigurationNamespace;
    }
}
