<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\NodeVisitor;

use Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Node\GetAttributeExpression;
use Twig\Environment;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class GetAttributeExpressionOverride implements NodeVisitorInterface
{
    public function enterNode(Node $node, Environment $env)
    {
        if (get_class($node) !== GetAttrExpression::class) {
            return $node;
        }

        $nodes = [
            'node' => $node->getNode('node'),
            'attribute' => $node->getNode('attribute')
        ];

        if ($node->hasNode('arguments')) {
            $nodes['arguments'] = $node->getNode('arguments');
        }

        $attributes = [
            'type' => $node->getAttribute('type'),
            'is_defined_test' => $node->getAttribute('is_defined_test'),
            'ignore_strict_check' => $node->getAttribute('ignore_strict_check'),
            'optimizable' => $node->getAttribute('optimizable'),
        ];

        return new GetAttributeExpression(
            $nodes,
            $attributes,
            $node->getTemplateLine(),
            $node->getNodeTag()
        );
    }

    public function leaveNode(Node $node, Environment $env)
    {
        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}
