<?php

namespace Netgen\EzPlatformSite\Tests\Integration;

/**
 * Base class for LoadService API integration tests.
 */
class LoadServiceBaseTest extends BaseTest
{
    protected function doTestLoadContent($data)
    {
        list(, $contentId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $content = $loadService->loadContent($contentId);
        $this->assertContent($content, $data);
    }

    protected function doTestLoadContentInExplicitVersionAndLanguage($data)
    {
        list(, $contentId) = array_values($data);
        $versionNo = $data['loadVersionNo'];
        $languageCode = $data['loadLanguageCode'];
        $loadService = $this->getSite()->getLoadService();
        $content = $loadService->loadContent($contentId, $versionNo, $languageCode);
        $this->assertContent($content, $data);
    }

    protected function doTestLoadContentByRemoteId($data)
    {
        list(, , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $content = $loadService->loadContentByRemoteId($remoteId);
        $this->assertContent($content, $data);
    }

    protected function doTestLoadContentInfo($data)
    {
        list(, $contentId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $contentInfo = $loadService->loadContentInfo($contentId);
        $this->assertContentInfo($contentInfo, $data);
    }

    protected function doTestLoadContentInfoInExplicitVersionAndLanguage($data)
    {
        list(, $contentId) = array_values($data);
        $versionNo = $data['loadVersionNo'];
        $languageCode = $data['loadLanguageCode'];
        $loadService = $this->getSite()->getLoadService();
        $contentInfo = $loadService->loadContentInfo($contentId, $versionNo, $languageCode);
        $this->assertContentInfo($contentInfo, $data);
    }

    protected function doTestLoadContentInfoByRemoteId($data)
    {
        list(, , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $contentInfo = $loadService->loadContentInfoByRemoteId($remoteId);
        $this->assertContentInfo($contentInfo, $data);
    }

    protected function doTestLoadLocation($data)
    {
        list(, , , $locationId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $location = $loadService->loadLocation($locationId);
        $this->assertLocation($location, $data);
    }

    protected function doTestLoadLocationByRemoteId($data)
    {
        list(, , , , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $location = $loadService->loadLocationByRemoteId($remoteId);
        $this->assertLocation($location, $data);
    }

    protected function doTestLoadNode($data)
    {
        list(, , , $locationId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $node = $loadService->loadNode($locationId);
        $this->assertContent($node, $data);
        $this->assertLocation($node->location, $data);
    }

    protected function doTestLoadNodeByRemoteId($data)
    {
        list(, , , , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $node = $loadService->loadNodeByRemoteId($remoteId);
        $this->assertContent($node, $data);
        $this->assertLocation($node->location, $data);
    }
}
