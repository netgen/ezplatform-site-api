<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\Core\QueryType\QueryType as BaseQueryTypeInterface;

/**
 * Extend the base QueryType interface with detection for a single supported parameter.
 */
interface QueryType extends BaseQueryTypeInterface
{
    /**
     * Check if the QueryType supports parameter with the given $name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function supportsParameter(string $name): bool;
}
