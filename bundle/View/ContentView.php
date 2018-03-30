<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use eZ\Publish\Core\MVC\Symfony\View\EmbedView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use RuntimeException;

/**
 * Builds ContentView objects.
 */
class ContentView extends BaseView implements View, ContentValueView, LocationValueView, EmbedView, CachableView
{
    /**
     * Name of the QueryCollection variable injected to the template.
     *
     * @see \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollection
     */
    const QUERY_COLLECTION_NAME = 'find';

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $content;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location|null
     */
    private $location;

    /**
     * @var bool
     */
    private $isEmbed = false;

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

    /**
     * Sets the value as embed / not embed.
     *
     * @param bool $value
     */
    public function setIsEmbed($value)
    {
        $this->isEmbed = (bool)$value;
    }

    /**
     * Is the view an embed or not.
     * @return bool True if the view is an embed, false if it is not.
     */
    public function isEmbed()
    {
        return $this->isEmbed;
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
