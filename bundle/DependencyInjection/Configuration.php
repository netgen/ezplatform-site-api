<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $rootNodeName;

    public function __construct(string $rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder($this->rootNodeName);
    }
}
