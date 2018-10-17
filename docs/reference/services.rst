Services
========

Site API provides hte following services that you can use in you PHP code:

.. contents::
    :depth: 3
    :local:

LoadService
-----------

+--------------------------------+----------------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\LoadService`` |
+--------------------------------+----------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.load_service``      |
+--------------------------------+----------------------------------------------+

The purpose of ``LoadService`` is to load Site Content and Locations by their ID.

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``loadContent()``
.................

Load Content object by it's ID.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Content object                                                                     |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $loadService->loadContent(42);                                      |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadContentByRemoteId()``
...........................

Load Content object by it's remote ID.

+----------------------------------------+----------------------------------------------------------------+
| **Parameters**                         | ``string $remoteId``                                           |
+----------------------------------------+----------------------------------------------------------------+
| **Returns**                            | Content object                                                 |
+----------------------------------------+----------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                            |
|                                        |                                                                |
|                                        |     $content = $loadService->loadContentByRemoteId('f2bfc25'); |
|                                        |                                                                |
+----------------------------------------+----------------------------------------------------------------+

``loadLocation()``
..................

Load Location object by it's ID.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Location object                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $loadService->loadLocation(42);                                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadLocationByRemoteId()``
............................

Load Location object by it's remote ID.

+----------------------------------------+-----------------------------------------------------------------+
| **Parameters**                         | ``string $remoteId``                                            |
+----------------------------------------+-----------------------------------------------------------------+
| **Returns**                            | Location object                                                 |
+----------------------------------------+-----------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                             |
|                                        |                                                                 |
|                                        |     $content = $loadService->loadLocationByRemoteId('a44fd4e'); |
|                                        |                                                                 |
+----------------------------------------+-----------------------------------------------------------------+

FindService
-----------

+--------------------------------+----------------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\FindService`` |
+--------------------------------+----------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.find_service``      |
+--------------------------------+----------------------------------------------+

The purpose of the ``FindService`` is to find Content and Locations by using eZ Platform's
Repository Search API. This service will use the search engine that is configured for the
Repository. That can be Legacy search engine or Solr search engine.

The service will return ``SearchResult`` object from the Repository API containing Site API objects.

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``findContent()``
.................

Find Content by the Content Query.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Location object                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $findService->findContent($query);                                  |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``findLocations()``
...................

Find Locations by the LocationQuery.

+----------------------------------------+-------------------------------------------------------------------+
| **Parameters**                         | ``eZ\Publish\API\Repository\Values\Content\LocationQuery $query`` |
+----------------------------------------+-------------------------------------------------------------------+
| **Returns**                            | ``eZ\Publish\API\Repository\Values\Content\Search\SearchResult``  |
+----------------------------------------+-------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                               |
|                                        |                                                                   |
|                                        |     $content = $findService->findLocations($locationQuery);       |
|                                        |                                                                   |
+----------------------------------------+-------------------------------------------------------------------+

FilterService
-------------

+--------------------------------+------------------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\FilterService`` |
+--------------------------------+------------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.load_service``        |
+--------------------------------+------------------------------------------------+

The purpose of the ``FindService`` is to find Content and Locations by using eZ Platform's
Repository Search API. That is the same as ``FindService``, but with the difference that it will
always use Legacy search engine.

While Solr search engine provides more features and more performance than Legacy search engine, it's
a separate system needs to be synchronized with changes in the database. This synchronization
comes with a delay, which can be a problem in some cases.

FilterService gives you access to search that is always up to date, because it uses Legacy search
engine that works directly with database. At the same time, search on top of Solr, with all the
advanced features (like fulltext search or facets) is still available through FindService.

The service will return ``SearchResult`` object from the Repository API containing Site API objects.

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``filterContent()``
...................

Filter Content by the Content Query.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Location object                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $filterService->filterContent($query);                              |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``filterLocations()``
.....................

Filter Locations by the LocationQuery.

+----------------------------------------+-------------------------------------------------------------------+
| **Parameters**                         | ``eZ\Publish\API\Repository\Values\Content\LocationQuery $query`` |
+----------------------------------------+-------------------------------------------------------------------+
| **Returns**                            | ``eZ\Publish\API\Repository\Values\Content\Search\SearchResult``  |
+----------------------------------------+-------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                               |
|                                        |                                                                   |
|                                        |     $content = $filterService->filterLocations($locationQuery);   |
|                                        |                                                                   |
+----------------------------------------+-------------------------------------------------------------------+

RelationService
---------------

+--------------------------------+--------------------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\RelationService`` |
+--------------------------------+--------------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.relation_service``      |
+--------------------------------+--------------------------------------------------+

The purpose of ``RelationService`` is to provide a way to load field relations. This needs to be
done respecting permissions and sort order and actually requires surprising amount of code when
using Repository API.

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``loadFieldRelation()``
.......................

Load single field relation from a specific field of a specific Content.

The method will return ``null`` if the field does not contain relations that can be loaded by the
current user. If the field contains multiple relations, the first one will be returned. The method
supports optional filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string|int $contentId``                                                       |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | ``Content`` or ``null``                                                            |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $relationService->loadFieldRelation(                                |
|                                        |         42,                                                                        |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadFieldRelations()``
........................

Load all field relations from a specific field of a specific Content. The method supports optional
filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string|int $contentId``                                                       |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | ``Content`` or ``null``                                                            |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $relationService->loadFieldRelations(                               |
|                                        |         42,                                                                        |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

Settings
--------

The purpose of ``Settings`` object is to provide read access to current configuration.

+--------------------------------+-------------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\Settings`` |
+--------------------------------+-------------------------------------------+
| **Container ID**               | ``netgen.ezplatform_site.settings``       |
+--------------------------------+-------------------------------------------+

Properties
~~~~~~~~~~

+--------------------------------+
| **Properties**                 |
+================================+
| ``$prioritizedLanguages``      |
+--------------------------------+
| ``$useAlwaysAvailable``        |
+--------------------------------+
| ``$rootLocationId``            |
+--------------------------------+

Site
----

The purpose of ``SiteService`` is to aggregate all other Site API services.

+-------------------------------+----------------------------------------+
| **Fully Qualified Class Name** | ``Netgen\EzPlatformSiteApi\API\Site`` |
+--------------------------------+---------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.site``       |
+--------------------------------+---------------------------------------+

Methods
~~~~~~~

+--------------------------------+
| **Methods**                    |
+================================+
| ``getLoadService()``           |
+--------------------------------+
| ``getFindService()``           |
+--------------------------------+
| ``getFilterService()``         |
+--------------------------------+
| ``getRelationService()``       |
+--------------------------------+
| ``getSettings()``              |
+--------------------------------+


