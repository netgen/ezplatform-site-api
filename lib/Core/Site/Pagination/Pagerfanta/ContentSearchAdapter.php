<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

/**
 * @deprecated since version 2.5, to be removed in 3.0. Use FindAdapter or FilterAdapter instead.
 *
 * Pagerfanta adapter for Netgen eZ Platform Site Content search.
 * Will return results as Site Content objects.
 */
class ContentSearchAdapter extends ContentSearchHitAdapter
{
    /**
     * Returns a slice of the results as Content objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
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
