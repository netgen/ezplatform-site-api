<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSettings;

/**
 * @internal
 *
 * Hint against API abstract class instead of this service:
 *
 * @see \Netgen\EzPlatformSiteApi\API\Settings
 */
final class Settings extends BaseSettings
{
    /**
     * @var string[]
     */
    private $prioritizedLanguages;

    /**
     * @var bool
     */
    private $useAlwaysAvailable;

    /**
     * @var int
     */
    private $rootLocationId;

    /**
     * @var bool
     */
    private $failOnMissingField;

    /**
     * @param string[] $prioritizedLanguages
     */
    public function __construct(
        array $prioritizedLanguages,
        bool $useAlwaysAvailable,
        int $rootLocationId,
        bool $failOnMissingField
    ) {
        $this->prioritizedLanguages = $prioritizedLanguages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
        $this->rootLocationId = $rootLocationId;
        $this->failOnMissingField = $failOnMissingField;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException
     */
    public function __get(string $property)
    {
        switch ($property) {
            case 'prioritizedLanguages':
                return $this->prioritizedLanguages;
            case 'useAlwaysAvailable':
                return $this->useAlwaysAvailable;
            case 'rootLocationId':
                return $this->rootLocationId;
            case 'failOnMissingField':
                return $this->failOnMissingField;
        }

        throw new PropertyNotFoundException($property, static::class);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException
     */
    public function __set(string $property, $value): void
    {
        throw new PropertyReadOnlyException($property, static::class);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException
     */
    public function __isset(string $property): bool
    {
        switch ($property) {
            case 'prioritizedLanguages':
            case 'useAlwaysAvailable':
            case 'rootLocationId':
            case 'failOnMissingField':
                return true;
        }

        throw new PropertyNotFoundException($property, static::class);
    }
}
