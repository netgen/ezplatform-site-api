<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

/**
 * @deprecated since version 2.5, to be removed in 3.0. Use FindAdapter or FilterAdapter instead.
 *
 * Pagerfanta adapter for Netgen eZ Platform Site Location search.
 * Will return results as Site Location objects.
 */
class LocationSearchAdapter extends LocationSearchHitAdapter
{
    /**
     * Returns a slice of the results as Location objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    public function getSlice($offset, $length)
    {
        $list = [];
        foreach (parent::getSlice($offset, $length) as $hit) {
            $list[] = $hit->valueObject;
        }

        return $list;
    }
}
