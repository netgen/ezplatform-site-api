<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

/**
 * @deprecated since version 2.2, to be removed in 3.0. Use ContentSearchAdapter instead.
 *
 * Pagerfanta adapter for Netgen eZ Platform Site ContentInfo search.
 * Will return results as Site ContentInfo objects.
 */
class ContentInfoSearchAdapter extends ContentInfoSearchHitAdapter
{
    /**
     * Returns a slice of the results as ContentInfo objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\ContentInfo[]
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
