<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Site Settings.
 *
 * @property-read array $prioritizedLanguages Array of prioritized languages
 * @property-read bool $useAlwaysAvailable Always available fallback state
 * @property-read int|string $rootLocationId Root Location ID
 * @property-read bool $failOnMissingFields Whether to fail on missing Content Fields
 */
abstract class Settings
{
}
