<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Event;

use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Component\EventDispatcher\Event;

final class RenderContentEvent extends Event
{
    /**
     * View object that was rendered.
     *
     * @var \eZ\Publish\Core\MVC\Symfony\View\View
     */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function getView(): View
    {
        return $this->view;
    }
}
