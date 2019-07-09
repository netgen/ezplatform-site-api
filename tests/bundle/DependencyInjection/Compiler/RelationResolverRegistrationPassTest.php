<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler\RelationResolverRegistrationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RelationResolverRegistrationPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setDefinition(
            'netgen.ezplatform_site.plugins.field_type.relation_resolver.registry',
            new Definition()
        );
    }

    public function testRegisterResolver()
    {
        $fieldTypeIdentifier = 'field_type_identifier';
        $serviceId = 'service_id';
        $definition = new Definition();
        $definition->addTag(
            'netgen.ezplatform_site.plugins.field_type.relation_resolver',
            ['identifier' => $fieldTypeIdentifier]
        );
        $this->setDefinition($serviceId, $definition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ezplatform_site.plugins.field_type.relation_resolver.registry',
            'register',
            [$fieldTypeIdentifier, $serviceId]
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testRegisterResolverWithoutIdentifier()
    {
        $serviceId = 'service_id';
        $definition = new Definition();
        $definition->addTag('netgen.ezplatform_site.plugins.field_type.relation_resolver');
        $this->setDefinition($serviceId, $definition);

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RelationResolverRegistrationPass());
    }
}
