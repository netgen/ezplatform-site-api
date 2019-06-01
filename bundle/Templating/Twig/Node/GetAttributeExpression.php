<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Node;

use Netgen\EzPlatformSiteApi\Core\Site\Values\Fields;
use Twig\Compiler;
use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\Source;
use Twig\Template;
use function twig_get_attribute;

final class GetAttributeExpression extends GetAttrExpression
{
    /**
     * @var \Twig\Node\Expression\GetAttrExpression
     */
    private $expression;

    /** @noinspection MagicMethodsValidityInspection */
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(GetAttrExpression $expression)
    {
        $this->expression = $expression;
    }

    public function compile(Compiler $compiler)
    {
        $env = $compiler->getEnvironment();

        // optimize array calls
        if (
            $this->getAttribute('optimizable')
            && !$this->getAttribute('is_defined_test')
            && Template::ARRAY_CALL === $this->getAttribute('type')
            && (!$env->isStrictVariables() || $this->getAttribute('ignore_strict_check'))
        ) {
            $var = '$'.$compiler->getVarName();
            $compiler
                ->raw('(('.$var.' = ')
                ->subcompile($this->getNode('node'))
                ->raw(') && is_array(')
                ->raw($var)
                ->raw(') || ')
                ->raw($var)
                ->raw(' instanceof ArrayAccess ? (')
                ->raw($var)
                ->raw('[')
                ->subcompile($this->getNode('attribute'))
                ->raw('] ?? null) : null)')
            ;

            return;
        }

        $compiler->raw(self::class . '::twig_get_attribute($this->env, $this->source, ');

        if ($this->getAttribute('ignore_strict_check')) {
            $this->getNode('node')->setAttribute('ignore_strict_check', true);
        }

        $compiler
            ->subcompile($this->getNode('node'))
            ->raw(', ')
            ->subcompile($this->getNode('attribute'))
        ;

        if ($this->hasNode('arguments')) {
            $compiler->raw(', ')->subcompile($this->getNode('arguments'));
        } else {
            $compiler->raw(', []');
        }

        $compiler->raw(', ')
            ->repr($this->getAttribute('type'))
            ->raw(', ')->repr($this->getAttribute('is_defined_test'))
            ->raw(', ')->repr($this->getAttribute('ignore_strict_check'))
            ->raw(', ')->repr($env->hasExtension(SandboxExtension::class))
            ->raw(', ')->repr($this->getNode('node')->getTemplateLine())
            ->raw(')')
        ;
    }

    public function __toString()
    {
        return $this->expression->__toString();
    }

    public function getTemplateLine()
    {
        return $this->expression->getTemplateLine();
    }

    public function getNodeTag()
    {
        return $this->expression->getNodeTag();
    }

    public function hasAttribute($name)
    {
        return $this->expression->hasAttribute($name);
    }

    public function getAttribute($name)
    {
        return $this->expression->getAttribute($name);
    }

    public function setAttribute($name, $value)
    {
        $this->expression->setAttribute($name, $value);
    }

    public function removeAttribute($name)
    {
        $this->expression->removeAttribute($name);
    }

    public function hasNode($name)
    {
        return $this->expression->hasNode($name);
    }

    public function getNode($name)
    {
        return $this->expression->getNode($name);
    }

    public function setNode($name, Node $node)
    {
        $this->expression->setNode($name, $node);
    }

    public function removeNode($name)
    {
        $this->expression->removeNode($name);
    }

    public function count()
    {
        return $this->expression->count();
    }

    public function getIterator()
    {
        return $this->expression->getIterator();
    }

    /**
     * {@inheritDoc}
     * @deprecated
     */
    public function setTemplateName($name)
    {
        $this->expression->setTemplateName($name);
    }

    public function getTemplateName()
    {
        return $this->expression->getTemplateName();
    }

    public function setSourceContext(Source $source)
    {
        $this->expression->setSourceContext($source);
    }

    public function getSourceContext()
    {
        return $this->expression->getSourceContext();
    }

    /**
     * @param \Twig\Environment $env
     * @param \Twig\Source $source
     * @param mixed $object
     * @param mixed $item
     * @param array $arguments
     * @param string $type
     * @param bool $isDefinedTest
     * @param bool $ignoreStrictCheck
     * @param bool $sandboxed
     * @param int $lineno
     *
     * @throws \Twig\Error\RuntimeError
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return mixed
     */
    public static function twig_get_attribute(
        Environment $env,
        Source $source,
        $object,
        $item,
        array $arguments = [],
        $type = 'any',
        $isDefinedTest = false,
        $ignoreStrictCheck = false,
        $sandboxed = false,
        $lineno = -1
    ) {
        if (!$object instanceof Fields) {
            return twig_get_attribute(
                $env,
                $source,
                $object,
                $item,
                $arguments,
                $type,
                $isDefinedTest,
                $ignoreStrictCheck,
                $sandboxed,
                $lineno
            );
        }

        if ($isDefinedTest) {
            return $object->hasField($item);
        }

        return $object->offsetGet($item);
    }
}
