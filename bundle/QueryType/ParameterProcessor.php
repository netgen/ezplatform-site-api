<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\RequestStack;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;

/**
 * ParameterProcessor processes query configuration parameter values using ExpressionLanguage.
 *
 * @internal do not depend on this service, it can be changed without warning
 */
final class ParameterProcessor
{
    /**
     * @var string
     */
    private const ExpressionMarker = '@=';

    /**
     * @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage
     */
    private $expressionLanguage;

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
        ExpressionLanguage $expressionLanguage,
        RequestStack $requestStack,
        ConfigResolverInterface $configResolver,
        Provider $namedObjectProvider
    ) {
        $this->expressionLanguage = $expressionLanguage;
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
     *
     * @return mixed
     */
    public function process($value, ContentView $view)
    {
        if (!$this->isExpression($value)) {
            return $value;
        }

        return $this->expressionLanguage->evaluate(
            $this->extractExpression($value),
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

    private function isExpression($value): bool
    {
        return is_string($value) && mb_strpos($value, self::ExpressionMarker) === 0;
    }

    private function extractExpression(string $value): string
    {
        return mb_substr($value, mb_strlen(self::ExpressionMarker));
    }
}
