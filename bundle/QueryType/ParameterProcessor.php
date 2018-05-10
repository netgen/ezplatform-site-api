<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

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
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
            function ($name, $default) {},
            function ($arguments, $name, $default) {
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
            function ($name, $default) {},
            function ($arguments, $name, $default) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                return $request->query->get($name, $default);
            }
        );

        $expressionLanguage->register(
            'timestamp',
            function ($timeString) {},
            function ($arguments, $timeString) {
                return strtotime($timeString);
            }
        );
    }
}
