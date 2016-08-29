<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Exceptions;

use Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException as APITranslationNotMatchedException;
use eZ\Publish\Core\Base\Exceptions\Httpable;
use eZ\Publish\Core\Base\Translatable;
use eZ\Publish\Core\Base\TranslatableBase;
use Exception;

/**
 * This exception is thrown if the Content translation language could not be resolved.
 */
class TranslationNotMatchedException extends APITranslationNotMatchedException implements Httpable, Translatable
{
    use TranslatableBase;

    /**
     * Generates: Could not match translation for Content '{$contentId}' in context '{$context}'.
     *
     * @param string|int $contentId
     * @param mixed $context
     * @param \Exception|null $previous
     */
    public function __construct($contentId, $context, Exception $previous = null)
    {
        $this->setMessageTemplate(
            "Could not match translation for Content '%contentId%' in context '%context%'"
        );
        $this->setParameters(
            [
                '%contentId%' => $contentId,
                '%context%' => var_export($context, true),
            ]
        );

        parent::__construct($this->getBaseTranslation(), self::NOT_FOUND, $previous);
    }
}
