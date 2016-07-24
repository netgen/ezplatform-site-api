<?php

namespace Netgen\EzPlatformSite\Core\Site\Pagination\Pagerfanta;

/**
 * Pagerfanta adapter for Netgen eZ Platform Site Content search.
 * Will return results as Site Content objects.
 */
class ContentSearchAdapter extends ContentSearchHitAdapter
{
    /**
     * Returns a slice of the results as Content objects.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     *
     * @return \Netgen\EzPlatformSite\API\Values\Content[]
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
