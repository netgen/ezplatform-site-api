<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Exceptions;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * This exception is thrown if the translation language could not be resolved.
 */
abstract class TranslationNotMatchedException extends NotFoundException
{
}
