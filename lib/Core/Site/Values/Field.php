<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;

final class Field extends APIField
{
    /**
     * @var int|string
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

    /**
     * @var bool
     */
    private $isSurrogate;

    public function __construct(array $properties = [])
    {
        $this->isEmpty = $properties['isEmpty'];
        $this->isSurrogate = $properties['isSurrogate'];

        unset($properties['isEmpty'], $properties['isSurrogate']);

        parent::__construct($properties);
    }

    public function __debugInfo(): array
    {
        return [
            'id' => $this->id,
            'fieldDefIdentifier' => $this->fieldDefIdentifier,
            'value' => $this->value,
            'languageCode' => $this->languageCode,
            'fieldTypeIdentifier' => $this->fieldTypeIdentifier,
            'name' => $this->name,
            'description' => $this->description,
            'content' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Content]',
            'contentId' => $this->content->id,
            'isEmpty' => $this->isEmpty,
            'isSurrogate' => $this->isSurrogate,
            'innerField' => '[An instance of eZ\Publish\API\Repository\Values\Content\Field]',
            'innerFieldDefinition' => $this->innerFieldDefinition,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    public function isSurrogate(): bool
    {
        return $this->isSurrogate;
    }
}
