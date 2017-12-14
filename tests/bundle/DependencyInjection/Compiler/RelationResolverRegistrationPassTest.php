<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Compiler;

use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler\RelationResolverRegistrationPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
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

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RelationResolverRegistrationPass());
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
}
