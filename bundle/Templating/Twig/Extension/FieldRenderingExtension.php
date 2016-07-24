<?php

namespace Netgen\EzPlatformSiteBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\EzPlatformSite\API\Values\Content;
use Netgen\EzPlatformSite\API\Values\Field;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistryInterface;
use eZ\Publish\Core\MVC\Symfony\Templating\FieldBlockRendererInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension for content fields/fieldDefinitions rendering (view and edit).
 */
class FieldRenderingExtension extends Twig_Extension
{
    /**
     * @var FieldBlockRendererInterface|\eZ\Publish\Core\MVC\Symfony\Templating\Twig\FieldBlockRenderer
     */
    private $fieldBlockRenderer;

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var ParameterProviderRegistryInterface
     */
    private $parameterProviderRegistry;

    public function __construct(
        FieldBlockRendererInterface $fieldBlockRenderer,
        ContentTypeService $contentTypeService,
        ParameterProviderRegistryInterface $parameterProviderRegistry
    ) {
        $this->fieldBlockRenderer = $fieldBlockRenderer;
        $this->contentTypeService = $contentTypeService;
        $this->parameterProviderRegistry = $parameterProviderRegistry;
    }

    public function getName()
    {
        return 'netgen.field_rendering';
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ng_render_field',
                function (Twig_Environment $environment, Content $content, $identifier, array $params = []) {
                    $this->fieldBlockRenderer->setTwig($environment);

                    return $this->renderField($content, $identifier, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        );
    }

    /**
     * Renders the HTML for a given field.
     *
     * @throws InvalidArgumentException
     *
     * @param \Netgen\EzPlatformSite\API\Values\Content $content
     * @param string $identifier Identifier for the field we want to render
     * @param array $params An array of parameters to pass to the field view
     *
     * @return string The HTML markup
     */
    public function renderField(Content $content, $identifier, array $params = [])
    {
        $field = $content->getField($identifier);

        if (!$field instanceof Field) {
            throw new InvalidArgumentException(
                '$identifier',
                "'{$identifier}' field not present on content #{$content->contentInfo->id} '{$content->contentInfo->name}'"
            );
        }

        $params = $this->getRenderFieldBlockParameters($content, $field, $params);

        return $this->fieldBlockRenderer->renderContentFieldView(
            $field->innerField,
            $field->typeIdentifier,
            $params
        );
    }

    /**
     * Generates the array of parameter to pass to the field template.
     *
     * @param \Netgen\EzPlatformSite\API\Values\Content $content
     * @param \Netgen\EzPlatformSite\API\Values\Field $field the Field to display
     * @param array $params An array of parameters to pass to the field view
     *
     * @return array
     */
    private function getRenderFieldBlockParameters(Content $content, Field $field, array $params = [])
    {
        // Merging passed parameters to default ones
        $params += [
            'parameters' => [], // parameters dedicated to template processing
            'attr' => [], // attributes to add on the enclosing HTML tags
        ];

        $versionInfo = $content->innerContent->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        $fieldDefinition = $contentType->getFieldDefinition($field->identifier);
        // Adding Field, FieldSettings and ContentInfo objects to
        // parameters to be passed to the template
        $params += [
            'field' => $field->innerField,
            'content' => $content->innerContent,
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'fieldSettings' => $fieldDefinition->getFieldSettings(),
        ];

        // Adding field type specific parameters if any.
        if ($this->parameterProviderRegistry->hasParameterProvider($fieldDefinition->fieldTypeIdentifier)) {
            $params['parameters'] += $this->parameterProviderRegistry
                ->getParameterProvider($fieldDefinition->fieldTypeIdentifier)
                ->getViewParameters($field->innerField);
        }

        // make sure we can easily add class="<fieldtypeidentifier>-field" to the
        // generated HTML
        if (isset($params['attr']['class'])) {
            $params['attr']['class'] .= " {$field->typeIdentifier}-field";
        } else {
            $params['attr']['class'] = "{$field->typeIdentifier}-field";
        }

        return $params;
    }
}
