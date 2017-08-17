<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;

final class Field extends APIField
{
    /**
     * @var string|int
     */
    protected $id;

    /**
     * @var string
     */
    protected $fieldDefIdentifier;

    /**
     * @var \eZ\Publish\SPI\FieldType\Value
     */
    protected $value;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var string
     */
    protected $fieldTypeIdentifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $innerField;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $innerFieldDefinition;

    /**
     * @var bool
     */
    private $isEmpty;

    public function __construct(array $properties = [])
    {
        if (isset($properties['isEmpty'])) {
            $this->isEmpty = $properties['isEmpty'];

            unset($properties['isEmpty']);
        }

        parent::__construct($properties);
    }

    public function isEmpty()
    {
        return $this->isEmpty;
    }
}
