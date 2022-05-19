<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\Variation;
use eZ\Publish\SPI\Variation\VariationHandler;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\API\Values\Field;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ImageRuntime
{
    /**
     * @var VariationHandler
     */
    private $imageVariationService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        VariationHandler $imageVariationService,
        LoggerInterface $logger = null
    ) {
        $this->imageVariationService = $imageVariationService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Values\Field $field
     * @param string $variationName
     *
     * @return null|\eZ\Publish\SPI\Variation\Values\Variation
     */
    public function getImageVariation(Field $field, string $variationName): Variation
    {
        /** @var \eZ\Publish\Core\FieldType\Image\Value $value */
        $value = $field->value;

        try {
            return $this->imageVariationService->getVariation($field->innerField, $field->content->versionInfo, $variationName);
        } catch (InvalidVariationException $e) {
            $this->logger->error("Couldn't get variation '{$variationName}' for image with id {$value->id}");
        } catch (SourceImageNotFoundException $e) {
            $this->logger->error(
                "Couldn't create variation '{$variationName}' for image with id {$value->id} because source image can't be found"
            );
        } catch (InvalidArgumentException $e) {
            $this->logger->error(
                "Couldn't create variation '{$variationName}' for image with id {$value->id} because an image could not be created from the given input"
            );
        }

        return new Variation();
    }
}
