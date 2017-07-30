<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\VariationHandler;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\API\Values\Field;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension
{
    /**
     * @var VariationHandler
     */
    private $imageVariationService;

    public function __construct(VariationHandler $imageVariationService)
    {
        $this->imageVariationService = $imageVariationService;
    }

    public function getName()
    {
        return 'netgen.image';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ng_image_alias',
                [$this, 'getImageVariation'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Values\Field $field
     * @param string $variationName
     *
     * @return \eZ\Publish\SPI\Variation\Values\Variation
     */
    public function getImageVariation(Field $field, $variationName)
    {
        try {
            return $this->imageVariationService->getVariation($field->innerField, $field->content->versionInfo, $variationName);
        } catch (InvalidVariationException $e) {
            if (isset($this->logger)) {
                $this->logger->error("Couldn't get variation '{$variationName}' for image with id {$field->value->id}");
            }
        } catch (SourceImageNotFoundException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' for image with id {$field->value->id} because source image can't be found"
                );
            }
        } catch (InvalidArgumentException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' for image with id {$field->value->id} because an image could not be created from the given input"
                );
            }
        }
    }
}
