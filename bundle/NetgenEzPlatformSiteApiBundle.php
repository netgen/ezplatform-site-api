<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle;

use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler\DefaultViewActionOverridePass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetgenEzPlatformSiteApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DefaultViewActionOverridePass());
    }
}
