<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

/**
 * @internal
 */
trait TranslatableTrait
{
    private function getTranslatedString($languageCode, $strings)
    {
        if (array_key_exists($languageCode, $strings)) {
            return $strings[$languageCode];
        }

        return null;
    }
}
