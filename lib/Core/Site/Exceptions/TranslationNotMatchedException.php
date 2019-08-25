<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Exceptions;

use Exception;
use eZ\Publish\Core\Base\Exceptions\Httpable;
use eZ\Publish\Core\Base\Translatable;
use eZ\Publish\Core\Base\TranslatableBase;
use Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException as APITranslationNotMatchedException;

/**
 * This exception is thrown if the Content translation language could not be resolved.
 */
class TranslationNotMatchedException extends APITranslationNotMatchedException implements Httpable, Translatable
{
    use TranslatableBase;

    /**
     * Generates: Could not match translation for Content '{$contentId}' in context '{$context}'.
     *
     * @param int|string $contentId
     * @param mixed $context
     * @param null|\Exception $previous
     */
    public function __construct($contentId, $context, Exception $previous = null)
    {
        $this->setMessageTemplate(
            "Could not match translation for Content '%contentId%' in context '%context%'"
        );
        $this->setParameters(
            [
                '%contentId%' => $contentId,
                '%context%' => \var_export($context, true),
            ]
        );

        parent::__construct($this->getBaseTranslation(), self::NOT_FOUND, $previous);
    }
}
