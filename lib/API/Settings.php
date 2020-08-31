<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Site Settings.
 *
 * @property array $prioritizedLanguages Array of prioritized languages
 * @property bool $useAlwaysAvailable Always available fallback state
 * @property int|string $rootLocationId Root Location ID
 * @property bool $failOnMissingField Whether to fail on a missing Content Field
 */
abstract class Settings
{
}
