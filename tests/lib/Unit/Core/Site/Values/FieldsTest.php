<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\Core\FieldType\Integer\Value;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\EzPlatformSiteApi\API\Values\Field as SiteField;
use Netgen\EzPlatformSiteApi\API\Values\Fields as APIFields;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Fields;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\ContentFieldsMockTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * Fields value unit tests.
 *
 * @group fields
 * @see \Netgen\EzPlatformSiteApi\API\Values\Fields
 */
class FieldsTest extends TestCase
{
    use ContentFieldsMockTrait;

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
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
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
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testDebugInfo()
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertEquals(
            array_values((array)$fields->getIterator()),
            $fields->__debugInfo()
        );
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
            new NullLogger()
        );
    }

    protected function internalGetRepoFieldDefinitions()
    {
        return [
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
        ];
    }

    protected function internalGetRepoFields()
    {
        return [
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
    }
}
