<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use Netgen\EzPlatformSite\API\Values\Content;
use Netgen\EzPlatformSite\API\Values\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as BaseContentView;

/**
 * Builds ContentView objects.
 */
class ContentView extends BaseContentView implements ContentValueView
{
    /**
     * @var \Netgen\EzPlatformSite\API\Values\Content
     */
    private $content;

    /**
     * @var \Netgen\EzPlatformSite\API\Values\Location|null
     */
    private $location;

    public function setSiteContent(Content $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content->innerContent;
    }

    public function setSiteLocation(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location->innerLocation;
    }

    public function getSiteContent()
    {
        return $this->content;
    }

    public function getSiteLocation()
    {
        return $this->location;
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
