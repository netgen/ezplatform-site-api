<?php

namespace Netgen\EzPlatformSite\Core\Persistence;

/**
 * @internal
 */
interface IdentifierMapProvider
{
    /**
     * Returns an array mapping ContentType IDs and identifiers in the form:.
     *
     * <code>
     *  [
     *      '<ContentType ID>' => [
     *          'identifier' => '<ContentType identifier>',
     *          'fieldDefinitions' => [
     *              '<FieldDefinition identifier>' => [
     *                  'id' => '<FieldDefinition ID>',
     *                  'typeIdentifier' => '<FieldType identifier>',
     *              ],
     *              ...
     *          ]
     *      ],
     *      ...
     *  ]
     * </code>
     *
     * @return array
     */
    public function getIdentifierMap();
}
