<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ParameterProcessor processes query configuration parameter values using ExpressionLanguage.
 *
 * @internal do not depend on this service, it can be changed without warning
 */
final class ParameterProcessor
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider
     */
    private $namedObjectProvider;

    public function __construct(
        RequestStack $requestStack,
        ConfigResolverInterface $configResolver,
        Provider $namedObjectProvider
    ) {
        $this->requestStack = $requestStack;
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
                'view' => $view,
                'location' => $view->getSiteLocation(),
                'content' => $view->getSiteContent(),
                'request' => $this->requestStack->getCurrentRequest(),
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
        $expressionLanguage->register(
            'viewParam',
            static function (): void {},
            static function (array $arguments, string $name, $default) {
                /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view */
                $view = $arguments['view'];

                if ($view->hasParameter($name)) {
                    return $view->getParameter($name);
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParam',
            static function (): void {},
            static function (array $arguments, string $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->get($name);

                if ($allowed === null || \in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamString',
            static function (): void {},
            static function (array $arguments, string $name, string $default, array $allowed = null): string {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = (string) $request->query->get($name);

                if ($allowed === null || \in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamInt',
            static function (): void {},
            static function (array $arguments, string $name, int $default, array $allowed = null): int {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->getInt($name);

                if ($allowed === null || \in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamFloat',
            static function (): void {},
            static function (array $arguments, string $name, float $default, array $allowed = null): float {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = (float) $request->query->get($name);

                if ($allowed === null || \in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamBool',
            static function (): void {},
            static function (array $arguments, string $name, bool $default, array $allowed = null): bool {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->getBoolean($name);

                if ($allowed === null || \in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'timestamp',
            static function (): void {},
            static function (array $arguments, string $timeString) {
                return \strtotime($timeString);
            }
        );

        $expressionLanguage->register(
            'config',
            static function (): void {},
            function (array $arguments, string $name, ?string $namespace = null, ?string $scope = null) {
                return $this->configResolver->getParameter($name, $namespace, $scope);
            }
        );

        $expressionLanguage->register(
            'namedContent',
            static function (): void {},
            function (array $arguments, string $name) {
                return $this->namedObjectProvider->getContent($name);
            }
        );

        $expressionLanguage->register(
            'namedLocation',
            static function (): void {},
            function (array $arguments, string $name) {
                return $this->namedObjectProvider->getLocation($name);
            }
        );

        $expressionLanguage->register(
            'namedTag',
            static function (): void {},
            function (array $arguments, string $name) {
                return $this->namedObjectProvider->getTag($name);
            }
        );

        $expressionLanguage->register(
            'split',
            static function (): void {},
            static function (array $arguments, string $name, string $delimiter = ',') {
                if (empty($name)) {
                    return null;
                }

                return \array_values(\array_filter(\array_map('\trim', \explode($delimiter, $name))));
            }
        );

        $expressionLanguage->register(
            'fieldValue',
            static function (): void {},
            static function (array $arguments, string $fieldIdentifier) {
                /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
                $content = $arguments['content'];

                return $content->getFieldValue($fieldIdentifier);
            }
        );
    }
}
