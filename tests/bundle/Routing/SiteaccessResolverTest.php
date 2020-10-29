<?php


namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\Routing;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\Repository\Values\Content\Location as CoreLocation;
use eZ\Publish\SPI\Persistence\Content\Handler as ContentHandler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\SPI\Persistence\Handler;
use Netgen\Bundle\EzPlatformSiteApiBundle\Routing\SiteaccessResolver;
use eZ\Publish\API\Repository\Values\Content\Location;
use PHPUnit\Framework\TestCase;
use function in_array;

/**
 * @group siteaccess
 */
class SiteaccessResolverTest extends TestCase
{
    public function providerForTestResolve(): array
    {
        return [
            'Same siteaccess is used' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'translation_siteaccesses' => ['eng', 'ger', 'ita'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['eng-GB', 'ger-DE'],
                        ],
                    ],
                ],
                'eng',
            ],
            'Matched siteaccess is used if no matching translation siteaccess is configured' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ger', 'ita'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ita-IT'],
                        ],
                    ],
                ],
                'eng',
            ],
            'Current siteaccess is preferred' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group_1' => ['eng'],
                            'frontend_group_2' => ['ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ger', 'ita'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'ger',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ita-IT'],
                        ],
                    ],
                ],
                'ita',
            ],
            'Order of translation siteaccesses is significant 1' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'translation_siteaccesses' => ['eng', 'ger', 'ita'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ger-DE', 'ita-IT'],
                        ],
                    ],
                ],
                'ger',
            ],
            'Order of translation siteaccesses is significant 2' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'translation_siteaccesses' => ['eng', 'ita', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ger-DE', 'ita-IT'],
                        ],
                    ],
                ],
                'ita',
            ],
            'Siteaccess not in translation siteaccesses list of the matched siteaccess is not used' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'translation_siteaccesses' => ['eng', 'ita', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['jpn-JP'],
                        ],
                    ],
                ],
                'eng',
            ],
            'Current siteaccess is used if language is allowed' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'translation_siteaccesses' => ['eng', 'ger', 'ita'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'ita-IT', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'ger',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ita-IT', 'eng-GB'],
                        ],
                    ],
                ],
                'ger',
            ],
            'When no translation siteaccesses are defined, matched (current) siteaccess is used' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita'],
                        'groups' => [
                            'frontend_group' => ['eng', 'ger', 'ita'],
                        ],
                    ],
                    'system' => [
                        'frontend_group' => [
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'ger',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/2/42/',
                            'languageCodes' => ['ita-IT'],
                        ],
                    ],
                ],
                'ger',
            ],
            'When no translation siteaccesses are defined, matched (first found) siteaccess is used' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ita'],
                            'frontend_group_2' => ['ger', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'ger',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['ita-IT'],
                        ],
                    ],
                ],
                'ger',
            ],
            'Tree root and translation siteaccess is matched' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ita', 'jpn'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'jpn',
            ],
            'Tree root and translation siteaccess is matched by priority' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'jpn', 'eng2'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['eng2', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['jpn', 'eng2'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                        'eng2' => ['languages' => ['eng-GB', 'jpn-JP']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'jpn',
            ],
            'Siteaccess is excluded' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ita', 'jpn'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'excluded_siteaccess_names' => ['jpn'],
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'ita',
            ],
            'Siteaccess group is excluded' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ita', 'jpn'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'excluded_siteaccess_group_names' => ['frontend_group_2'],
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'eng',
            ],
            'Siteaccess and siteaccess group are excluded' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ita', 'jpn'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'ger',
                        'excluded_siteaccess_names' => ['eng'],
                        'excluded_siteaccess_group_names' => ['frontend_group_2'],
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'ger',
            ],
            'Current siteaccess is used by default' => [
                [
                    'siteaccess' => [
                        'list' => ['eng', 'ger', 'ita', 'jpn'],
                        'groups' => [
                            'frontend_group_1' => ['eng', 'ger'],
                            'frontend_group_2' => ['ita', 'jpn'],
                        ],
                    ],
                    'system' => [
                        'frontend_group_1' => [
                            'translation_siteaccesses' => ['eng', 'ger'],
                            'content' => ['tree_root' => ['location_id' => 2]],
                        ],
                        'frontend_group_2' => [
                            'translation_siteaccesses' => ['ita', 'jpn'],
                            'content' => ['tree_root' => ['location_id' => 200]],
                        ],
                        'eng' => ['languages' => ['eng-GB']],
                        'ger' => ['languages' => ['ger-DE', 'eng-GB']],
                        'ita' => ['languages' => ['ita-IT', 'eng-GB']],
                        'jpn' => ['languages' => ['jpn-JP', 'eng-GB']],
                    ],
                    '_context' => [
                        'current_siteaccess' => 'eng',
                        'excluded_siteaccess_names' => ['eng'],
                        'excluded_siteaccess_group_names' => ['frontend_group_2'],
                        'location' => [
                            'id' => 42,
                            'pathString' => '/1/200/42/',
                            'languageCodes' => ['jpn-JP', 'eng-GB'],
                        ],
                    ],
                ],
                'eng',
            ],
        ];
    }

    /**
     * @dataProvider providerForTestResolve
     */
    public function testResolve(array $data, string $expectedSiteaccessName): void
    {
        $siteaccessResolver = $this->getSiteaccessResolverUnderTest($data);
        $location = $this->getMockedLocation($data);

        self::assertSame($expectedSiteaccessName, $siteaccessResolver->resolve($location));
    }

    protected function getMockedLocation(array $data): Location
    {
        $data = $data['_context']['location'];

        return new CoreLocation([
            'id' => $data['id'],
            'pathString' => $data['pathString'],
            'contentInfo' => new ContentInfo([
                'id' => 24,
                'alwaysAvailable' => false,
            ]),
        ]);
    }

    protected function getSiteaccessResolverUnderTest(array $data): SiteaccessResolver
    {
        $siteaccessResolver = new SiteaccessResolver(
            $this->persistenceHandlerMock($data),
            $this->getExcludedSiteaccessNames($data),
            $this->getExcludedSiteaccessGroupNames($data)
        );

        $siteaccessResolver->setConfigResolver($this->getConfigResolverMock($data));
        $siteaccessResolver->setSiteaccess($this->getSiteaccess($data));
        $siteaccessResolver->setSiteaccessGroupsBySiteaccess($this->getSiteaccessGroupsBySiteaccess($data));
        $siteaccessResolver->setSiteaccessList($this->getSiteaccessList($data));

        return $siteaccessResolver;
    }

    protected function persistenceHandlerMock(array $data): Handler
    {
        $versionInfo = new VersionInfo([
            'languageCodes' => $data['_context']['location']['languageCodes'],
        ]);

        $contentHandlerMock = $this->createMock(ContentHandler::class);
        $contentHandlerMock->method('loadVersionInfo')->willReturn($versionInfo);

        $persistenceHandler = $this->createMock(Handler::class);
        $persistenceHandler->method('contentHandler')->willReturn($contentHandlerMock);

        return $persistenceHandler;
    }

    protected function getExcludedSiteaccessNames(array $data): array
    {
        return $data['_context']['excluded_siteaccess_names'] ?? [];
    }

    protected function getExcludedSiteaccessGroupNames(array $data): array
    {
        return $data['_context']['excluded_siteaccess_group_names'] ?? [];
    }

    protected function getConfigResolverMock(array $data): ConfigResolverInterface
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $getParameterValueMap = $this->getConfigResolverReturnValueMap($data);

        $configResolver->method('hasParameter')->willReturn(true);
        $configResolver->method('getParameter')->will(self::returnValueMap($getParameterValueMap));

        return $configResolver;
    }

    protected function getConfigResolverReturnValueMap(array $data): array
    {
        $valueMap = [];
        $siteaccessConfigMap = $this->getSiteaccessConfigMap($data);

        foreach ($siteaccessConfigMap as $siteaccess => $config) {
            $valueMap[] = ['languages', null, $siteaccess, $config['languages'] ?? []];
            $valueMap[] = ['translation_siteaccesses', null, $siteaccess, $config['translation_siteaccesses'] ?? []];
            $valueMap[] = ['content.tree_root.location_id', null, $siteaccess, $config['content']['tree_root']['location_id'] ?? null];
        }

        return $valueMap;
    }

    protected function getSiteaccessConfigMap(array $data): array
    {
        $siteaccesses = $data['siteaccess']['list'];
        $map = [];

        foreach ($siteaccesses as $siteaccess) {
            $group = $this->getSiteaccessGroupBySiteaccess($siteaccess, $data);
            $siteaccessConfig = $data['system'][$siteaccess] ?? [];
            $groupConfig = $data['system'][$group] ?? [];

            $map[$siteaccess] = $siteaccessConfig + $groupConfig;
        }

        return $map;
    }

    protected function getSiteaccessGroupBySiteaccess(string $siteaccess, array $data): ?string
    {
        $groups = $data['siteaccess']['groups'] ?? [];

        foreach ($groups as $group => $groupSiteaccesses) {
            if (in_array($siteaccess, $groupSiteaccesses, true)) {
                return $group;
            }
        }

        return null;
    }

    protected function getSiteaccess(array $data): SiteAccess
    {
        return new SiteAccess($data['_context']['current_siteaccess']);
    }

    protected function getSiteaccessGroupsBySiteaccess(array $data): array
    {
        $map = [];
        $groups = $data['siteaccess']['groups'] ?? [];

        foreach ($groups as $group => $siteaccesses) {
            foreach ($siteaccesses as $siteaccess) {
                $map[$siteaccess][] = $group;
            }
        }

        return $map;
    }

    protected function getSiteaccessList(array $data): array
    {
        return $data['siteaccess']['list'];
    }
}
