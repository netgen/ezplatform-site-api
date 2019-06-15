<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use ArrayIterator;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\API\Values\Fields as APIField;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * @internal do not depend on this implementation, use API Fields instead
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\Values\Fields
 */
final class Fields extends APIField
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
        APIContent $content,
        DomainObjectMapper $domainObjectMapper,
        $failOnMissingFields,
        LoggerInterface $logger = null
    ) {
        $this->content = $content;
        $this->domainObjectMapper = $domainObjectMapper;
        $this->failOnMissingFields = $failOnMissingFields;
        $this->logger = $logger === null ? new NullLogger() : $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getIterator()
    {
        $this->initialize();

        return $this->iterator;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function offsetExists($identifier)
    {
        $this->initialize();

        return array_key_exists($identifier, $this->fieldsByIdentifier)
            || array_key_exists($identifier, $this->fieldsByNumericSequence);
    }

    /**
     * @param $identifier
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return bool
     */
    public function hasField($identifier)
    {
        $this->initialize();

        return array_key_exists($identifier, $this->fieldsByIdentifier);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function offsetGet($identifier)
    {
        $this->initialize();

        if (array_key_exists($identifier, $this->fieldsByIdentifier)) {
            return $this->fieldsByIdentifier[$identifier];
        }

        if (array_key_exists($identifier, $this->fieldsByNumericSequence)) {
            return $this->fieldsByNumericSequence[$identifier];
        }

        $message = sprintf('Field "%s" in Content #%s does not exist', $identifier, $this->content->id);

        if ($this->failOnMissingFields) {
            throw new RuntimeException($message);
        }

        $this->logger->critical($message . ', using null field instead');

        return $this->domainObjectMapper->getNullField($identifier, $this->content);
    }

    public function offsetSet($identifier, $value)
    {
        throw new RuntimeException('Setting the field to the collection is not allowed');
    }

    public function offsetUnset($identifier)
    {
        throw new RuntimeException('Unsetting the field from the collection is not allowed');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function count()
    {
        $this->initialize();

        return count($this->fieldsByIdentifier);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function hasFieldById($id)
    {
        $this->initialize();

        return array_key_exists($id, $this->fieldsById);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFieldById($id)
    {
        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id];
        }

        $message = sprintf('Field #%s in Content #%s does not exist', $id, $this->content->id);

        if ($this->failOnMissingFields) {
            throw new RuntimeException($message);
        }

        $this->logger->critical($message . ', using null field instead');

        return $this->domainObjectMapper->getNullField((string)$id, $this->content);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function __debugInfo()
    {
        $this->initialize();

        return array_values($this->fieldsByIdentifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function initialize()
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
}