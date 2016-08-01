<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

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
     * @var ParameterProviderRegistryInterface
     */
    private $parameterProviderRegistry;

    public function __construct(
        FieldBlockRendererInterface $fieldBlockRenderer,
        ParameterProviderRegistryInterface $parameterProviderRegistry
    ) {
        $this->fieldBlockRenderer = $fieldBlockRenderer;
        $this->parameterProviderRegistry = $parameterProviderRegistry;
    }

    public function getName()
    {
        return 'netgen.field_rendering';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ng_render_field',
                function (Twig_Environment $environment, Field $field, array $params = []) {
                    $this->fieldBlockRenderer->setTwig($environment);
                    return $this->renderField($field, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Renders the HTML for a given field.
     *
     * @throws InvalidArgumentException
     *
     * @param \Netgen\EzPlatformSite\API\Values\Field $field
     * @param array $params An array of parameters to pass to the field view
     *
     * @return string The HTML markup
     */
    public function renderField(Field $field, array $params = [])
    {
        $params = $this->getRenderFieldBlockParameters($field, $params);

        return $this->fieldBlockRenderer->renderContentFieldView(
            $field->innerField,
            $field->fieldTypeIdentifier,
            $params
        );
    }

    /**
     * Generates the array of parameter to pass to the field template.
     *
     * @param \Netgen\EzPlatformSite\API\Values\Field $field the Field to display
     * @param array $params An array of parameters to pass to the field view
     *
     * @return array
     */
    private function getRenderFieldBlockParameters(Field $field, array $params = [])
    {
        // Merging passed parameters to default ones
        $params += [
            'parameters' => [], // parameters dedicated to template processing
            'attr' => [], // attributes to add on the enclosing HTML tags
        ];

        $content = $field->content->innerContent;
        $contentType = $field->content->contentInfo->innerContentType;
        $fieldDefinition = $contentType->getFieldDefinition($field->identifier);

        $params += [
            'field' => $field->innerField,
            'content' => $content,
            'contentInfo' => $content->getVersionInfo()->getContentInfo(),
            'versionInfo' => $content->getVersionInfo(),
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
            $params['attr']['class'] .= " {$field->fieldTypeIdentifier}-field";
        } else {
            $params['attr']['class'] = "{$field->fieldTypeIdentifier}-field";
        }

        return $params;
    }
}
