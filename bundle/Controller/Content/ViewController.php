<?php

namespace Netgen\EzPlatformSiteBundle\Controller\Content;

use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use Netgen\EzPlatformSiteBundle\View\ContentView;

/**
 * This controller provides the content view feature.
 */
class ViewController extends Controller
{
    /**
     * This is the default view action or a ContentView object.
     *
     * It doesn't do anything by itself: the returned View object is rendered by the ViewRendererListener
     * into an HttpFoundation Response.
     *
     * This action can be selectively replaced by a custom action by means of content_view
     * configuration. Custom actions can add parameters to the view and customize the Response the View will be
     * converted to. They may also bypass the ViewRenderer by returning an HttpFoundation Response.
     *
     * Cache is in both cases handled by the CacheViewResponseListener.
     *
     * @param \Netgen\EzPlatformSiteBundle\View\ContentView $view
     *
     * @return \Netgen\EzPlatformSiteBundle\View\ContentView
     */
    public function viewAction(ContentView $view)
    {
        return $view;
    }

    /**
     * Embed a content.
     * Behaves mostly like viewAction(), but with specific content load permission handling.
     *
     * @param \Netgen\EzPlatformSiteBundle\View\ContentView $view
     *
     * @return \Netgen\EzPlatformSiteBundle\View\ContentView
     */
    public function embedAction(ContentView $view)
    {
        return $view;
    }
}
