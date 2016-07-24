<?php

namespace Netgen\EzPlatformSite\Core\Site\Pagination\Pagerfanta;

/**
 * Pagerfanta adapter for Netgen eZ Platform Site Node search.
 * Will return results as Site Node objects.
 */
class NodeSearchAdapter extends NodeSearchHitAdapter
{
    /**
     * Returns a slice of the results as Location objects.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     *
     * @return \Netgen\EzPlatformSite\API\Values\Node[]
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
