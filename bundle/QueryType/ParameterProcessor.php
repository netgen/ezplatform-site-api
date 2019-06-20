<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ParameterProcessor processes query configuration parameter values using ExpressionLanguage.
 *
 * @internal Do not depend on this service, it can be changed without warning.
 */
final class ParameterProcessor
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var  \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(RequestStack $requestStack, ConfigResolverInterface $configResolver)
    {
        $this->requestStack = $requestStack;
        $this->configResolver = $configResolver;
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
        if (!is_string($value) || 0 !== strpos($value, '@=')) {
            return $value;
        }

        $language = new ExpressionLanguage();

        $this->registerFunctions($language);

        return $language->evaluate(
            substr($value, 2),
            [
                'view' => $view,
                'location' => $view->getSiteLocation(),
                'content' => $view->getSiteContent(),
                'request' => $this->requestStack->getCurrentRequest(),
                'configResolver' => $this->configResolver,
            ]
        );
    }

    /**
     * Register functions with the given $expressionLanguage.
     *
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $expressionLanguage
     */
    private function registerFunctions(ExpressionLanguage $expressionLanguage)
    {
        $expressionLanguage->register(
            'viewParam',
            static function () {},
            static function ($arguments, $name, $default) {
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
            static function () {},
            static function ($arguments, $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->get($name);

                if ($allowed === null || in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamString',
            static function () {},
            static function ($arguments, $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = (string)$request->query->get($name);

                if ($allowed === null || in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamInt',
            static function () {},
            static function ($arguments, $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->getInt($name);

                if ($allowed === null || in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamFloat',
            static function () {},
            static function ($arguments, $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = (float)$request->query->get($name);

                if ($allowed === null || in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'queryParamBool',
            static function () {},
            static function ($arguments, $name, $default, array $allowed = null) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                if (!$request->query->has($name)) {
                    return $default;
                }

                $value = $request->query->getBoolean($name);

                if ($allowed === null || in_array($value, $allowed, true)) {
                    return $value;
                }

                return $default;
            }
        );

        $expressionLanguage->register(
            'timestamp',
            static function () {},
            static function ($arguments, $timeString) {
                return strtotime($timeString);
            }
        );

        $configResolver = $this->configResolver;

        $expressionLanguage->register(
            'config',
            static function () {},
            static function ($arguments, $name, $namespace = null, $scope = null) use ($configResolver) {
                return $configResolver->getParameter($name, $namespace, $scope);
            }
        );
    }
}
