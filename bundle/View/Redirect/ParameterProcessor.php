<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect;

use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ParameterProcessor
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider
     */
    private $namedObjectProvider;

    public function __construct(
        Provider $namedObjectProvider
    ) {
        $this->namedObjectProvider = $namedObjectProvider;
    }

    /**
     * Return given $value processed with ExpressionLanguage if needed.
     *
     * Parameter $view is used to provide values for evaluation.
     *
     * @param mixed $value
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return mixed
     */
    public function process($value, ContentView $view)
    {
        if (!\is_string($value) || \strpos($value, '@=') !== 0) {
            return $value;
        }

        $language = new ExpressionLanguage();

        $this->registerFunctions($language);

        return $language->evaluate(
            \substr($value, 2),
            [
                'location' => $view->getSiteLocation(),
                'content' => $view->getSiteContent(),
                'namedObject' => $this->namedObjectProvider,
            ]
        );
    }

    /**
     * Register functions with the given $expressionLanguage.
     *
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $expressionLanguage
     */
    private function registerFunctions(ExpressionLanguage $expressionLanguage): void
    {
        $namedObjectProvider = $this->namedObjectProvider;

        $expressionLanguage->register(
            'namedContent',
            static function (): void {},
            static function (array $arguments, string $name) use ($namedObjectProvider) {
                return $namedObjectProvider->getContent($name);
            }
        );

        $expressionLanguage->register(
            'namedLocation',
            static function (): void {},
            static function (array $arguments, string $name) use ($namedObjectProvider) {
                return $namedObjectProvider->getLocation($name);
            }
        );

        $expressionLanguage->register(
            'namedTag',
            static function (): void {},
            static function (array $arguments, string $name) use ($namedObjectProvider) {
                return $namedObjectProvider->getTag($name);
            }
        );
    }

}
