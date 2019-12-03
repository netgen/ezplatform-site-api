<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use ArrayIterator;
use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\EzPlatformSiteApi\API\Values\Content as RepoContent;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;
use Netgen\EzPlatformSiteApi\API\Values\Fields as APIFields;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field\SurrogateValue;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Traversable;

/**
 * @internal do not depend on this implementation, use API Fields instead
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Fields
 */
final class Fields extends APIFields
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $content;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private $domainObjectMapper;

    /**
     * @var bool
     */
    private $failOnMissingFields;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $areFieldsInitialized = false;

    /**
     * @var \ArrayIterator
     */
    private $iterator;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private $fieldsByIdentifier = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private $fieldsById = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private $fieldsByNumericSequence = [];

    public function __construct(
        RepoContent $content,
        DomainObjectMapper $domainObjectMapper,
        bool $failOnMissingFields,
        LoggerInterface $logger
    ) {
        $this->content = $content;
        $this->domainObjectMapper = $domainObjectMapper;
        $this->failOnMissingFields = $failOnMissingFields;
        $this->logger = $logger;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function __debugInfo(): array
    {
        $this->initialize();

        return $this->fieldsByIdentifier;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getIterator(): Traversable
    {
        $this->initialize();

        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function offsetExists($identifier): bool
    {
        $this->initialize();

        return \array_key_exists($identifier, $this->fieldsByIdentifier)
            || \array_key_exists($identifier, $this->fieldsByNumericSequence);
    }

    /**
     * @param string $identifier
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return bool
     */
    public function hasField(string $identifier): bool
    {
        $this->initialize();

        return \array_key_exists($identifier, $this->fieldsByIdentifier);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getField(string $identifier): APIField
    {
        if ($this->hasField($identifier)) {
            return $this->fieldsByIdentifier[$identifier];
        }

        $message = \sprintf('Field "%s" in Content #%s does not exist', $identifier, $this->content->id);

        if ($this->failOnMissingFields) {
            throw new RuntimeException($message);
        }

        $this->logger->critical($message . ', using surrogate field instead');

        return $this->getSurrogateField($identifier, $this->content);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function offsetGet($identifier): APIField
    {
        $this->initialize();

        if (\array_key_exists($identifier, $this->fieldsByIdentifier)) {
            return $this->fieldsByIdentifier[$identifier];
        }

        if (\array_key_exists($identifier, $this->fieldsByNumericSequence)) {
            return $this->fieldsByNumericSequence[$identifier];
        }

        $message = \sprintf('Field "%s" in Content #%s does not exist', $identifier, $this->content->id);

        if ($this->failOnMissingFields) {
            throw new RuntimeException($message);
        }

        $this->logger->critical($message . ', using surrogate field instead');

        return $this->getSurrogateField($identifier, $this->content);
    }

    public function offsetSet($identifier, $value): void
    {
        throw new RuntimeException('Setting the field to the collection is not allowed');
    }

    public function offsetUnset($identifier): void
    {
        throw new RuntimeException('Unsetting the field from the collection is not allowed');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function count(): int
    {
        $this->initialize();

        return \count($this->fieldsByIdentifier);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function hasFieldById($id): bool
    {
        $this->initialize();

        return \array_key_exists($id, $this->fieldsById);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFieldById($id): APIField
    {
        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id];
        }

        $message = \sprintf('Field #%s in Content #%s does not exist', $id, $this->content->id);

        if ($this->failOnMissingFields) {
            throw new RuntimeException($message);
        }

        $this->logger->critical($message . ', using surrogate field instead');

        return $this->getSurrogateField((string) $id, $this->content);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFirstNonEmptyField(string $firstIdentifier, string ...$otherIdentifiers): APIField
    {
        $identifiers = \array_merge([$firstIdentifier], $otherIdentifiers);
        $fields = $this->getAvailableFields($identifiers);

        foreach ($fields as $field) {
            if (!$field->isEmpty()) {
                return $field;
            }
        }

        return $fields[0] ?? $this->getSurrogateField($firstIdentifier, $this->content);
    }

    /**
     * @param string[] $identifiers
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private function getAvailableFields(array $identifiers): array
    {
        $fields = [];

        foreach ($identifiers as $identifier) {
            if ($this->hasField($identifier)) {
                $fields[] = $this->getField($identifier);
            }
        }

        return $fields;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function initialize(): void
    {
        if ($this->areFieldsInitialized) {
            return;
        }

        $content = $this->content;

        foreach ($content->innerContent->getFieldsByLanguage($content->languageCode) as $apiField) {
            $field = $this->domainObjectMapper->mapField($apiField, $content);

            $this->fieldsByIdentifier[$field->fieldDefIdentifier] = $field;
            $this->fieldsById[$field->id] = $field;
            $this->fieldsByNumericSequence[] = $field;
            $this->iterator = new ArrayIterator($this->fieldsByIdentifier);
        }

        $this->areFieldsInitialized = true;
    }

    private function getSurrogateField(string $identifier, SiteContent $content): Field
    {
        $apiField = new RepoField([
            'id' => 0,
            'fieldDefIdentifier' => $identifier,
            'value' => new SurrogateValue(),
            'languageCode' => $content->languageCode,
            'fieldTypeIdentifier' => 'ngsurrogate',
        ]);

        $fieldDefinition = new CoreFieldDefinition([
            'id' => 0,
            'identifier' => $apiField->fieldDefIdentifier,
            'fieldGroup' => '',
            'position' => 0,
            'fieldTypeIdentifier' => $apiField->fieldTypeIdentifier,
            'isTranslatable' => false,
            'isRequired' => false,
            'isInfoCollector' => false,
            'defaultValue' => null,
            'isSearchable' => false,
            'mainLanguageCode' => $apiField->languageCode,
            'fieldSettings' => [],
            'validatorConfiguration' => [],
        ]);

        return new Field([
            'id' => $apiField->id,
            'fieldDefIdentifier' => $fieldDefinition->identifier,
            'value' => $apiField->value,
            'languageCode' => $apiField->languageCode,
            'fieldTypeIdentifier' => $apiField->fieldTypeIdentifier,
            'name' => '',
            'description' => '',
            'content' => $content,
            'innerField' => $apiField,
            'innerFieldDefinition' => $fieldDefinition,
            'isEmpty' => true,
            'isSurrogate' => true,
        ]);
    }
}
