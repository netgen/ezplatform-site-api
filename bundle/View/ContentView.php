<?php

namespace Netgen\EzPlatformSiteBundle\View;

use Netgen\EzPlatformSite\API\Values\Content;
use Netgen\EzPlatformSite\API\Values\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as PlatformContentView;

/**
 * Builds ContentView objects.
 */
class ContentView extends PlatformContentView
{
    /**
     * @var \Netgen\EzPlatformSite\API\Values\Content
     */
    private $content;

    /**
     * @var \Netgen\EzPlatformSite\API\Values\Location|null
     */
    private $location;

    /**
     * @param \Netgen\EzPlatformSite\API\Values\Content $content
     */
    public function setSiteContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Returns the Content.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function getContent()
    {
        return $this->content->innerContent;
    }

    /**
     * @param \Netgen\EzPlatformSite\API\Values\Location $location
     */
    public function setSiteLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function getLocation()
    {
        return $this->location->innerLocation;
    }

    protected function getInternalParameters()
    {
        $parameters = ['content' => $this->content];
        if ($this->location !== null) {
            $parameters['location'] = $this->location;
        }

        return $parameters;
    }
}
