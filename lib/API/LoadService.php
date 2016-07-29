<?php

namespace Netgen\EzPlatformSite\API;

/**
 * Load service provides methods for loading entities by their ID.
 */
interface LoadService
{
    /**
     * Loads Content object for the given $contentId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $contentId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Content
     */
    public function loadContent($contentId);

    /**
     * Loads Content object for the given $remoteId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $remoteId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Content
     */
    public function loadContentByRemoteId($remoteId);

    /**
     * Loads ContentInfo object for the given $contentId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $contentId
     *
     * @return \Netgen\EzPlatformSite\API\Values\ContentInfo
     */
    public function loadContentInfo($contentId);

    /**
     * Loads ContentInfo object for the given $remoteId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $remoteId
     *
     * @return \Netgen\EzPlatformSite\API\Values\ContentInfo
     */
    public function loadContentInfoByRemoteId($remoteId);

    /**
     * Loads Location object for the given $locationId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $locationId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Location
     */
    public function loadLocation($locationId);

    /**
     * Loads Location object for the given $remoteId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $remoteId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Location
     */
    public function loadLocationByRemoteId($remoteId);

    /**
     * Loads Node object for the given $locationId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $locationId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Node
     */
    public function loadNode($locationId);

    /**
     * Loads Node object for the given $remoteId.
     *
     * @throws \Netgen\EzPlatformSite\API\Exceptions\TranslationNotMatchedException
     *
     * @param string|int $remoteId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Node
     */
    public function loadNodeByRemoteId($remoteId);
}
