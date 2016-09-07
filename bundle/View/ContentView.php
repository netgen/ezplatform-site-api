<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as BaseContentView;
use RuntimeException;

/**
 * Builds ContentView objects.
 */
class ContentView extends BaseContentView implements ContentValueView
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $content;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location|null
     */
    private $location;

    public function setSiteContent(Content $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        if (!$this->content instanceof Content) {
            return null;
        }

        return $this->content->innerContent;
    }

    public function setSiteLocation(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        if (!$this->location instanceof Location) {
            return null;
        }

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

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    public function setContent(APIContent $content)
    {
        throw new RuntimeException(
            'setContent method cannot be used with Site API content view. Use setSiteContent method instead.'
        );
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    public function setLocation(APILocation $location)
    {
        throw new RuntimeException(
            'setLocation method cannot be used with Site API content view. Use setSiteLocation method instead.'
        );
    }
}
