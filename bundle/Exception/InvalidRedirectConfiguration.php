<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Exception;

final class InvalidRedirectConfiguration extends \Exception
{
    public function __construct(string $target)
    {
        $message = "Not possible to resolve redirect from given target: '{$target}'";

        parent::__construct($message);
    }
}
