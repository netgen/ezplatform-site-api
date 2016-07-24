<?php

namespace Netgen\EzPlatformSite\Core\Persistence\Legacy\IdentifierMap\Gateway;

use Netgen\EzPlatformSite\Core\Persistence\Legacy\IdentifierMap\Gateway;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use PDO;

/**
 * Doctrine database based identifier map gateway.
 */
class DoctrineDatabase extends Gateway
{
    /**
     * @var \eZ\Publish\Core\Persistence\Database\DatabaseHandler
     */
    protected $dbHandler;

    /**
     * @param \eZ\Publish\Core\Persistence\Database\DatabaseHandler $dbHandler
     */
    public function __construct(DatabaseHandler $dbHandler)
    {
        $this->dbHandler = $dbHandler;
    }

    public function getIdentifierMapData()
    {
        $query = $this->dbHandler->createSelectQuery();
        $query
            ->select(
                $this->dbHandler->alias(
                    $this->dbHandler->quoteColumn('id', 'ezcontentclass'),
                    $this->dbHandler->quoteIdentifier('content_type_id')
                ),
                $this->dbHandler->alias(
                    $this->dbHandler->quoteColumn('identifier', 'ezcontentclass'),
                    $this->dbHandler->quoteIdentifier('content_type_identifier')
                ),
                $this->dbHandler->alias(
                    $this->dbHandler->quoteColumn('id', 'ezcontentclass_attribute'),
                    $this->dbHandler->quoteIdentifier('field_definition_id')
                ),
                $this->dbHandler->alias(
                    $this->dbHandler->quoteColumn('identifier', 'ezcontentclass_attribute'),
                    $this->dbHandler->quoteIdentifier('field_definition_identifier')
                ),
                $this->dbHandler->alias(
                    $this->dbHandler->quoteColumn('data_type_string', 'ezcontentclass_attribute'),
                    $this->dbHandler->quoteIdentifier('field_type_identifier')
                )
            )
            ->from(
                $this->dbHandler->quoteTable('ezcontentclass_attribute')
            )
            ->innerJoin(
                $this->dbHandler->quoteTable('ezcontentclass'),
                $query->expr->lAnd(
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('version', 'ezcontentclass'),
                        $query->bindValue(Type::STATUS_DEFINED, null, PDO::PARAM_INT)
                    ),
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('contentclass_id', 'ezcontentclass_attribute'),
                        $this->dbHandler->quoteColumn('id', 'ezcontentclass')
                    )
                )
            );

        $statement = $query->prepare();
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
