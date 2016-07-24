<?php

namespace Netgen\EzPlatformSite\Core\Persistence\Legacy\IdentifierMap;

/**
 * Base class for identifier map gateways.
 */
abstract class Gateway
{
    /**
     * Returns searchable identifier mapping data.
     *
     * @return array
     */
    abstract public function getIdentifierMapData();
}
