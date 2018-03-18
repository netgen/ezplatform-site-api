<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * QueryTypeListener processes query type configuration, contained in the View parameters.
 * Result of processing is a QueryCollection, which will be assigned to the View parameter.
 */
class QueryTypeListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper
     */
    private $queryCollectionMapper;

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper $queryCollectionMapper
     */
    public function __construct(QueryCollectionMapper $queryCollectionMapper)
    {
        $this->queryCollectionMapper = $queryCollectionMapper;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => [
                'processQueryTypeConfiguration', -100,
            ],
        ];
    }

    /**
     * Process query type configuration in the Content view contained in the given $event.
     *
     * @param \eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent $event
     */
    public function processQueryTypeConfiguration(PreContentViewEvent $event)
    {
        /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view */
        $view = $event->getContentView();
        $queryCollection = $this->queryCollectionMapper->map($view);

        $view->addParameters([
            'queries' => $queryCollection,
        ]);
    }
}
