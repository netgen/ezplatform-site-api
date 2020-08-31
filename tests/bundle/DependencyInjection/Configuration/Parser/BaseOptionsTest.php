<?php


namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\BaseOptions;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @group config
 *
 * @internal
 */
class BaseOptionsTest extends AbstractParserTestCase
{
    public function getBooleanConfigurationNames(): array
    {
        return [
            'ng_set_site_api_as_primary_content_view',
            'ng_fallback_to_secondary_content_view',
            'ng_fallback_without_subrequest',
            'ng_richtext_embed_without_subrequest',
            'ng_xmltext_embed_without_subrequest',
            'ng_use_always_available_fallback',
            'ng_fail_on_missing_field',
            'ng_render_missing_field_info',
        ];
    }

    public function getBooleanConfigurationValidValuePairs(): array
    {
        return [
            [
                true,
                true,
            ],
            [
                false,
                false,
            ],
        ];
    }

    public function providerForTestBooleanConfigurationValid(): \Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $valuePairs = $this->getBooleanConfigurationValidValuePairs();

        foreach ($names as $name) {
            foreach ($valuePairs as $valuePair) {
                yield [
                    $name,
                    $valuePair[0],
                    $valuePair[1],
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestBooleanConfigurationValid
     *
     * @param string $name
     * @param mixed $config
     * @param mixed $expectedValue
     */
    public function testBooleanConfigurationValid(string $name, $config, $expectedValue): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    $name => $config,
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            $name,
            $expectedValue,
            'ezdemo_site'
        );
    }

    public function getBooleanConfigurationInvalidValues(): array
    {
        return [
            0,
            1,
            'true',
            'false',
            [],
        ];
    }

    public function providerForTestBooleanConfigurationInvalid(): \Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $values = $this->getBooleanConfigurationInvalidValues();

        foreach ($names as $name) {
            foreach ($values as $value) {
                yield [
                    $name,
                    $value,
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestBooleanConfigurationInvalid
     *
     * @param string $name
     * @param mixed $config
     */
    public function testBooleanConfigurationInvalid(string $name, $config): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    $name => $config,
                ],
            ],
        ]);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new BaseOptions(),
            ]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return Yaml::parse(\file_get_contents(__DIR__ . '/../../Fixtures/minimal.yml'));
    }
}
