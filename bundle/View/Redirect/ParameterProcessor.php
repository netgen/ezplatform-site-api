<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ParameterProcessor
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider
     */
    private $namedObjectProvider;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Provider $namedObjectProvider
    ) {
        $this->configResolver = $configResolver;
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
                'configResolver' => $this->configResolver,
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
        $configResolver = $this->configResolver;

        $expressionLanguage->register(
            'config',
            static function (): void {},
            static function (array $arguments, string $name, ?string $namespace = null, ?string $scope = null) use ($configResolver) {
                return $configResolver->getParameter($name, $namespace, $scope);
            }
        );

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
    }

}
