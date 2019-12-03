<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\Core\FieldType\Integer\Value;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\API\Values\Field as SiteField;
use Netgen\EzPlatformSiteApi\API\Values\Fields as APIFields;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Fields;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\ContentFieldsMockTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * Fields value unit tests.
 *
 * @group fields
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Fields
 *
 * @internal
 */
final class FieldsTest extends TestCase
{
    use ContentFieldsMockTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    public function testFieldsCanBeCounted(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertEquals(3, \count($fields));
    }

    /**
     * @depends testFieldsCanBeCounted
     */
    public function testFieldsCanBeIterated(): void
    {
        $fields = $this->getFieldsUnderTest(true);
        $i = 1;

        foreach ($fields as $field) {
            $this->assertInstanceOf(APIFields::class, $fields);
            $this->assertEquals($i, $field->id);
            ++$i;
        }
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfExistingFieldCanBeCheckedByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue($fields->hasField('first'));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfNonExistingFieldCanBeCheckedByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse($fields->hasField('fourth'));
    }

    public function testExistenceOfExistingFieldCanBeCheckedAsAnArrayByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue(isset($fields['first']));
    }

    public function testExistenceOfNonExistingFieldCanBeCheckedAsAnArrayByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse(isset($fields['fourth']));
    }

    public function testExistenceOfExistingFieldCanBeCheckedAsAnArrayByNumericIndex(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue(isset($fields[0]));
    }

    public function testExistenceOfNonExistingFieldCanBeCheckedAsAnArrayByNumericIndex(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse(isset($fields[101]));
    }

    public function testFieldsCanBeAccessedAsAnArrayByNumericIndex(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        for ($i = 0; $i < 3; ++$i) {
            $field = $fields[$i];
            $this->assertInstanceOf(SiteField::class, $field);
            $this->assertEquals($i + 1, $field->id);
        }
    }

    public function testFieldsCanBeAccessedAsAnArrayByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);
        $identifiers = ['first', 'second', 'third'];

        foreach ($identifiers as $identifier) {
            $field = $fields[$identifier];
            $this->assertInstanceOf(SiteField::class, $field);
            $this->assertEquals($identifier, $field->fieldDefIdentifier);
        }
    }

    public function testAccessingNonExistentFieldThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);

        $fields = $this->getFieldsUnderTest(true);

        $fields['fourth'];
    }

    public function testAccessingNonExistentFieldReturnsNullField(): void
    {
        $fields = $this->getFieldsUnderTest(false);
        $identifier = 'fourth';

        $loggerMock = $this->getLoggerMock();
        $loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with('Field "fourth" in Content #1 does not exist, using surrogate field instead');

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Field $field */
        $field = $fields[$identifier];

        $this->assertInstanceOf(SiteField::class, $field);
        $this->assertEquals($identifier, $field->fieldDefIdentifier);
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    public function testFieldCanNotBeSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Setting the field to the collection is not allowed');

        $fields = $this->getFieldsUnderTest(true);

        /* @noinspection OnlyWritesOnParameterInspection */
        $fields['pekmez'] = 'džem';
    }

    public function testFieldCanNotBeUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsetting the field from the collection is not allowed');

        $fields = $this->getFieldsUnderTest(true);

        unset($fields['first']);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfExistingFieldCanBeCheckedById(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertTrue($fields->hasFieldById(1));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistenceOfNonExistingFieldCanBeCheckedById(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertFalse($fields->hasFieldById(101));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistingFieldCanBeAccessedById(): void
    {
        $fields = $this->getFieldsUnderTest(true);
        $id = 1;

        $field = $fields->getFieldById($id);

        $this->assertEquals($id, $field->id);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testNonExistentFieldCanNotBeAccessedById(): void
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
    public function testAccessingNonExistentFieldByIdReturnsNullField(): void
    {
        $id = 101;

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFieldById($id);

        $this->assertEquals((string) $id, $field->fieldDefIdentifier);
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testExistingFieldCanBeAccessedByIdentifier(): void
    {
        $fields = $this->getFieldsUnderTest(true);
        $identifier = 'first';

        $field = $fields->getField($identifier);

        $this->assertEquals($identifier, $field->fieldDefIdentifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testNonExistentFieldCanNotBeAccessedByIdentifier(): void
    {
        $identifier = 'fourth';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Field "%s" in Content #1 does not exist', $identifier));

        $fields = $this->getFieldsUnderTest(true);

        $fields->getField($identifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testAccessingNonExistentFieldByIdentifierReturnsNullField(): void
    {
        $identifier = 'fourth';

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getField($identifier);

        $this->assertEquals($identifier, $field->fieldDefIdentifier);
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testFirstNonEmptyFieldReturnsFirstField(): void
    {
        $identifier = 'first';

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFirstNonEmptyField($identifier, 'second', 'third', 'fourth');

        $this->assertEquals($identifier, $field->fieldDefIdentifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testFirstNonEmptyFieldReturnsFirstNonEmptyField(): void
    {
        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFirstNonEmptyField('1st', 'second', 'third', 'fourth');

        $this->assertEquals('third', $field->fieldDefIdentifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testFirstNonEmptyFieldReturnsThirdField(): void
    {
        $identifier = 'third';

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFirstNonEmptyField('1st', '2nd', $identifier, 'fourth');

        $this->assertEquals($identifier, $field->fieldDefIdentifier);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testFirstNonEmptyFieldReturnsSurrogateField(): void
    {
        $identifier = '1st';

        $fields = $this->getFieldsUnderTest(false);

        $field = $fields->getFirstNonEmptyField($identifier, '2nd', '3rd', '4th');

        $this->assertEquals($identifier, $field->fieldDefIdentifier);
        $this->assertEquals('ngsurrogate', $field->fieldTypeIdentifier);
        $this->assertTrue($field->isEmpty());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testDebugInfo(): void
    {
        $fields = $this->getFieldsUnderTest(true);

        $this->assertEquals(
            (array) $fields->getIterator(),
            $fields->__debugInfo()
        );
    }

    /**
     * @param bool $failOnMissingFields
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Fields
     */
    protected function getFieldsUnderTest(bool $failOnMissingFields): Fields
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
    protected function getLoggerMock(): MockObject
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
    protected function getMockedContent(): APIContent
    {
        return new Content(
            [
                'id' => 1,
                'site' => $this->getSiteMock(),
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'innerVersionInfo' => $this->getRepoVersionInfo(),
                'innerContent' => $this->getRepoContent(),
                'languageCode' => 'eng-GB',
            ],
            true,
            new NullLogger()
        );
    }

    protected function internalGetRepoFieldDefinitions(): array
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

    protected function internalGetRepoFields(): array
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
                'value' => new Value(),
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
