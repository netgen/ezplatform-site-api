<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\Core\FieldType\Integer\Value;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
use eZ\Publish\Core\Repository\Values\Content\Content as RepoContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\FieldType;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Field as SiteField;
use Netgen\EzPlatformSiteApi\API\Values\Fields as APIFields;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Fields;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Fields value unit tests.
 *
 * @group fields
 * @see \Netgen\EzPlatformSiteApi\API\Values\Fields
 */
class FieldsTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected $siteMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected $domainObjectMapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected $repositoryMock;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    protected $repoVersionInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $repoContent;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    protected $internalFields;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\SPI\FieldType\FieldType
     */
    private $fieldTypeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    public function testFieldsCanBeCounted()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertEquals(3, count($fields));
    }

    /**
     * @depends testFieldsCanBeCounted
     */
    public function testFieldsCanBeIterated()
    {
        $fields = $this->getFieldsUnderTest(true);
        $i = 1;

        foreach ($fields as $field) {
            $this->assertInstanceOf(APIFields::class, $fields);
            $this->assertEquals($i, $field->id);
            $i++;
        }
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfExistingFieldCanBeCheckedByIdentifier()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue($fields->hasField('first'));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfNonExistingFieldCanBeCheckedByIdentifier()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse($fields->hasField('fourth'));
    }

    public function testExistenceOfExistingFieldCanBeCheckedAsAnArrayByIdentifier()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue(isset($fields['first']));
    }

    public function testExistenceOfNonExistingFieldCanBeCheckedAsAnArrayByIdentifier()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse(isset($fields['fourth']));
    }

    public function testExistenceOfExistingFieldCanBeCheckedAsAnArrayByNumericIndex()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue(isset($fields[0]));
    }

    public function testExistenceOfNonExistingFieldCanBeCheckedAsAnArrayByNumericIndex()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse(isset($fields[101]));
    }

    public function testFieldsCanBeAccessedAsAnArrayByNumericIndex()
    {
        $fields = $this->getFieldsUnderTest(true);

        for ($i = 0; $i < 3; $i++) {
            $field = $fields[$i];
            $this->assertInstanceOf(SiteField::class, $field);
            $this->assertEquals($i + 1, $field->id);
        }
    }

    public function testFieldsCanBeAccessedAsAnArrayByIdentifier()
    {
        $fields = $this->getFieldsUnderTest(true);
        $identifiers = ['first', 'second', 'third'];

        foreach ($identifiers as $identifier) {
            $field = $fields[$identifier];
            $this->assertInstanceOf(SiteField::class, $field);
            $this->assertEquals($identifier, $field->fieldDefIdentifier);
        }
    }

    public function testAccessingNonExistentFieldThrowsRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        $fields = $this->getFieldsUnderTest(true);

        $fields['fourth'];
    }

    public function testAccessingNonExistentFieldReturnsNullField()
    {
        $fields = $this->getFieldsUnderTest(false);
        $identifier = 'fourth';

        $loggerMock = $this->getLoggerMock();
        $loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with('Field "fourth" in Content #1 does not exist, using null field instead');

        $field = $fields[$identifier];

        $this->assertInstanceOf(SiteField::class, $field);
        $this->assertEquals($identifier, $field->fieldDefIdentifier);
        $this->assertEquals('ngnull', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    public function testFieldCanNotBeSet()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Setting the field to the collection is not allowed');

        $fields = $this->getFieldsUnderTest(true);

        /** @noinspection OnlyWritesOnParameterInspection */
        $fields['pekmez'] = 'džem';
    }

    public function testFieldCanNotBeUnset()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsetting the field from the collection is not allowed');

        $fields = $this->getFieldsUnderTest(true);

        unset($fields['first']);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfExistingFieldCanBeCheckedById()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue($fields->hasFieldById(1));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfNonExistingFieldCanBeCheckedById()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse($fields->hasFieldById(101));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistingFieldCanBeAccessedById()
    {
        $fields = $this->getFieldsUnderTest(true);
        $id = 1;

        $field = $fields->getFieldById($id);

        $this->assertInstanceOf(SiteField::class, $field);
        $this->assertEquals($id, $field->id);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testNonExistentFieldCanNotBeAccessedById()
    {
        $id = 101;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Field #{$id} in Content #1 does not exist");

        $fields = $this->getFieldsUnderTest(true);

        $fields->getFieldById($id);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testAccessingNonExistentFieldByIdReturnsNullField()
    {
        $id = 101;

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFieldById($id);

        $this->assertInstanceOf(SiteField::class, $field);
        $this->assertEquals((string)$id, $field->fieldDefIdentifier);
        $this->assertEquals('ngnull', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    public function testDebugDump()
    {
        $fields = $this->getFieldsUnderTest(true);

        ob_start();
        var_dump($fields);
        $dump = ob_get_clean();

        $this->assertLessThan(8192, strlen($dump));
    }

    /**
     * @param bool $failOnMissingFields
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Fields
     */
    protected function getFieldsUnderTest($failOnMissingFields)
    {
        return new Fields(
            $this->getMockedContent(),
            $this->getDomainObjectMapper(),
            $failOnMissingFields,
            $this->getLoggerMock()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected function getLoggerMock()
    {
        if ($this->loggerMock !== null) {
            return $this->loggerMock;
        }

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();

        return $this->loggerMock;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected function getMockedContent()
    {
        return new Content(
            [
                'id' => 1,
                'site' => $this->getSiteMock(),
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'innerVersionInfo' => $this->getRepoVersionInfo(),
                'innerContent' => $this->getRepoContent(),
            ],
            true,
            null
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSiteMock()
    {
        if ($this->siteMock !== null) {
            return $this->siteMock;
        }

        $this->siteMock = $this->getMockBuilder(Site::class)->getMock();

        return $this->siteMock;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected function getDomainObjectMapper()
    {
        if ($this->domainObjectMapper !== null) {
            return $this->domainObjectMapper;
        }

        $this->domainObjectMapper = new DomainObjectMapper(
            $this->getSiteMock(),
            $this->getRepositoryMock(),
            true
        );

        return $this->domainObjectMapper;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected function getRepositoryMock()
    {
        if ($this->repositoryMock !== null) {
            return $this->repositoryMock;
        }

        $this->repositoryMock = $this
            ->getMockBuilder(CoreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repoContent = $this->getRepoContent();
        $this->repositoryMock->method('sudo')->willReturn($repoContent);

        $contentTypeServiceMock = $this->getContentTypeServiceMock();
        $this->repositoryMock->method('getContentTypeService')->willReturn($contentTypeServiceMock);

        $fieldTypeServiceMock = $this->getFieldTypeServiceMock();
        $this->repositoryMock->method('getFieldTypeService')->willReturn($fieldTypeServiceMock);

        return $this->repositoryMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected function getFieldTypeServiceMock()
    {
        if ($this->fieldTypeServiceMock !== null) {
            return $this->fieldTypeServiceMock;
        }

        $this->fieldTypeServiceMock = $this
            ->getMockBuilder(FieldTypeService::class)
            ->getMock();

        $fieldTypeMock = $this->getFieldTypeMock();
        $this->fieldTypeServiceMock
            ->method('getFieldType')
            ->willReturn($fieldTypeMock);

        return $this->fieldTypeServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\SPI\FieldType\FieldType
     */
    protected function getFieldTypeMock()
    {
        if ($this->fieldTypeMock !== null) {
            return $this->fieldTypeMock;
        }

        $this->fieldTypeMock = $this
            ->getMockBuilder(FieldType::class)
            ->getMock();

        $this->fieldTypeMock
            ->method('isEmptyValue')
            ->willReturn(false);

        return $this->fieldTypeMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeServiceMock()
    {
        if (null !== $this->contentTypeServiceMock) {
            return $this->contentTypeServiceMock;
        }

        $this->contentTypeServiceMock = $this->getMockBuilder(ContentTypeService::class)->getMock();

        $this->contentTypeServiceMock
            ->method('loadContentType')
            ->with('contentTypeId')
            ->willReturn(
                new ContentType([
                    'id' => 42,
                    'identifier' => 'test',
                    'fieldDefinitions' => [
                        new FieldDefinition([
                            'id' => 1,
                            'identifier' => 'first',
                        ]),
                        new FieldDefinition([
                            'id' => 2,
                            'identifier' => 'second',
                        ]),
                        new FieldDefinition([
                            'id' => 3,
                            'identifier' => 'third',
                        ]),
                    ],
                ])
            );

        return $this->contentTypeServiceMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Values\Content\Content
     */
    protected function getRepoContent()
    {
        if ($this->repoContent !== null) {
            return $this->repoContent;
        }

        $repoVersionInfo = $this->getRepoVersionInfo();

        $this->repoContent = new RepoContent([
            'versionInfo' => $repoVersionInfo,
            'internalFields' => $this->getInternalFields(),
        ]);

        return $this->repoContent;
    }

    protected function getInternalFields()
    {
        if ($this->internalFields !== null) {
            return $this->internalFields;
        }

        $this->internalFields = [
            new RepoField([
                'id' => 1,
                'fieldDefIdentifier' => 'first',
                'value' => new Value(1),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezinteger',
            ]),
            new RepoField([
                'id' => 2,
                'fieldDefIdentifier' => 'second',
                'value' => new Value(2),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezinteger',
            ]),
            new RepoField([
                'id' => 3,
                'fieldDefIdentifier' => 'third',
                'value' => new Value(3),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezinteger',
            ]),
        ];

        return $this->internalFields;
    }

    protected function getRepoVersionInfo()
    {
        if ($this->repoVersionInfo !== null) {
            return $this->repoVersionInfo;
        }

        $repoContentInfo = new RepoContentInfo([
            'id' => 1,
            'ownerId' => 'ownerId',
            'contentTypeId' => 'contentTypeId',
            'mainLanguageCode' => 'eng-GB',
        ]);

        $this->repoVersionInfo = new VersionInfo([
            'contentInfo' => $repoContentInfo,
        ]);

        return $this->repoVersionInfo;
    }
}
