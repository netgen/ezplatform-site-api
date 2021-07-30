Services
========

First thing to know about the Site API services is that all of them handle language configuration in
a completely transparent way. You can be sure that all objects you work with:

1. can be rendered on the current siteaccess
2. are loaded in the single correct translation to be rendered on the current siteaccess

This works for both Content and Locations, whether they are obtained through search, loading by the
ID, as relations or otherwise. If the object doesn't have a translation that can be rendered on a
siteaccess, you won't be able to load it in the first place. That means you can put the whole
language logic off your mind and solve real problems instead.

Following services are available:

.. contents::
    :depth: 3
    :local:

LoadService
-----------

+--------------------------------+----------------------------------------------+
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\LoadService`` |
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

Load Content object by its ID.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | :ref:`Content object<content_object>`                                              |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $loadService->loadContent(42);                                      |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadContentByRemoteId()``
...........................

Load Content object by its remote ID.

+----------------------------------------+----------------------------------------------------------------+
| **Parameters**                         | ``string $remoteId``                                           |
+----------------------------------------+----------------------------------------------------------------+
| **Returns**                            | :ref:`Content object<content_object>`                          |
+----------------------------------------+----------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                            |
|                                        |                                                                |
|                                        |     $content = $loadService->loadContentByRemoteId('f2bfc25'); |
|                                        |                                                                |
+----------------------------------------+----------------------------------------------------------------+

``loadLocation()``
..................

Load Location object by its ID.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string|int $id``                                                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | :ref:`Location object<location_object>`                                            |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $loadService->loadLocation(42);                                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadLocationByRemoteId()``
............................

Load Location object by its remote ID.

+----------------------------------------+-----------------------------------------------------------------+
| **Parameters**                         | ``string $remoteId``                                            |
+----------------------------------------+-----------------------------------------------------------------+
| **Returns**                            | :ref:`Location object<location_object>`                         |
+----------------------------------------+-----------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                             |
|                                        |                                                                 |
|                                        |     $content = $loadService->loadLocationByRemoteId('a44fd4e'); |
|                                        |                                                                 |
+----------------------------------------+-----------------------------------------------------------------+

FindService
-----------

+--------------------------------+----------------------------------------------+
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\FindService`` |
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
| **Parameters**                         | ``eZ\Publish\API\Repository\Values\Content\Query $query``                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | ``eZ\Publish\API\Repository\Values\Content\Search\SearchResult``                   |
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
|                                        |     $locations = $findService->findLocations($locationQuery);     |
|                                        |                                                                   |
+----------------------------------------+-------------------------------------------------------------------+

FilterService
-------------

+--------------------------------+------------------------------------------------+
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\FilterService`` |
+--------------------------------+------------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.filter_service``      |
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
| **Returns**                            | :ref:`Location object<location_object>`                                            |
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
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\RelationService`` |
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

Get single field relation :ref:`Content<content_object>` from a specific field of a given Content.

The method will return ``null`` if the field does not contain relations that can be loaded by the
current user. If the field contains multiple relations, the first one will be returned. The method
supports optional filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``Netgen\EzPlatformSiteApi\API\Values\Content $content``                        |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | :ref:`Content<content_object>` or ``null``                                         |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $relationService->loadFieldRelation(                                |
|                                        |         $content,                                                                  |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadFieldRelations()``
........................

Get all field relation :ref:`Content items<content_object>` from a specific field of a given Content. The method supports optional
filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``Netgen\EzPlatformSiteApi\API\Values\Content $content``                        |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of :ref:`Content items<content_object>`                                   |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $contentItems = $relationService->loadFieldRelations(                          |
|                                        |         $content,                                                                  |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadFieldRelationLocation()``
...............................

Get single field relation :ref:`Location<location_object>` from a specific field of a given Content.

The method will return ``null`` if the field does not contain relations that can be loaded by the
current user. If the field contains multiple relations, the first one will be returned. The method
supports optional filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``Netgen\EzPlatformSiteApi\API\Values\Content $content``                        |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | :ref:`Location<location_object>` or ``null``                                       |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $content = $relationService->loadFieldRelationLocation(                        |
|                                        |         $content,                                                                  |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``loadFieldRelationLocations()``
................................

Get all field relation :ref:`Locations<location_object>` from a specific field of a given Content. The method supports optional
filtering by ContentType.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``Netgen\EzPlatformSiteApi\API\Values\Content $content``                        |
|                                        | 2. ``string $fieldDefinitionIdentifier``                                           |
|                                        | 3. ``array $contentTypeIdentifiers = []``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of :ref:`Locations<location_object>`                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $contentItems = $relationService->loadFieldRelationLocations(                  |
|                                        |         $content,                                                                  |
|                                        |         'relations',                                                               |
|                                        |         ['articles']                                                               |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

Settings
--------

The purpose of ``Settings`` object is to provide read access to current configuration.

+--------------------------------+-------------------------------------------+
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\Settings`` |
+--------------------------------+-------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.settings``       |
+--------------------------------+-------------------------------------------+

Properties
~~~~~~~~~~

+--------------------------------+-----------------+-------------------------------------------------------------------+
| Property                       | Type            | Description                                                       |
+================================+=================+===================================================================+
| ``$prioritizedLanguages``      | ``string[]``    | An array of prioritized languages of the current siteaccess       |
+--------------------------------+-----------------+-------------------------------------------------------------------+
| ``$useAlwaysAvailable``        | ``bool``        | | Whether always available Content is taken into account          |
|                                |                 | | when resolving translations                                     |
+--------------------------------+-----------------+-------------------------------------------------------------------+
| ``$rootLocationId``            | ``string|int``  | Root Location of the current siteaccess                           |
+--------------------------------+-----------------+-------------------------------------------------------------------+

Site
----

The purpose of ``Site`` service is to aggregate all other Site API services in one place. It
implements a getter method for each of the services described above.

+--------------------------------+---------------------------------------+
| **Instance of**                | ``Netgen\EzPlatformSiteApi\API\Site`` |
+--------------------------------+---------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.site``       |
+--------------------------------+---------------------------------------+

Methods
~~~~~~~

+--------------------------------+--------------------------------+
| Method                         | Returns                        |
+================================+================================+
| ``getLoadService()``           | `LoadService`_                 |
+--------------------------------+--------------------------------+
| ``getFindService()``           | `FindService`_                 |
+--------------------------------+--------------------------------+
| ``getFilterService()``         | `FilterService`_               |
+--------------------------------+--------------------------------+
| ``getRelationService()``       | `RelationService`_             |
+--------------------------------+--------------------------------+
| ``getSettings()``              | `Settings`_                    |
+--------------------------------+--------------------------------+

.. _named_object_php:

NamedObjectProvider
-------------------

The purpose of ``NamedObjectProvider`` service is to provide access to named objects. Configuration
of named objects is :ref:`documented on the Configuration page<named_object_configuration>`.

+--------------------------------+----------------------------------------------------------------+
| **Instance of**                | ``Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider`` |
+--------------------------------+----------------------------------------------------------------+
| **Container service ID**       | ``netgen.ezplatform_site.named_object_provider``               |
+--------------------------------+----------------------------------------------------------------+

The purpose of ``NamedObjectProvider`` is to provide access to named objects.

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``hasContent()``
.................

Check if Content object with given name is configured.

+----------------------------------------+-------------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                        |
+----------------------------------------+-------------------------------------------------------------------------+
| **Returns**                            | ``boolean``                                                             |
+----------------------------------------+-------------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                     |
|                                        |                                                                         |
|                                        |     $hasCertificate = $namedObjectProvider->hasContent('certificate');  |
|                                        |                                                                         |
+----------------------------------------+-------------------------------------------------------------------------+

``getContent()``
.................

Get Content object by its name.

+----------------------------------------+----------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                     |
+----------------------------------------+----------------------------------------------------------------------+
| **Returns**                            | :ref:`Content object<content_object>`                                |
+----------------------------------------+----------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                  |
|                                        |                                                                      |
|                                        |     $certificate = $namedObjectProvider->getContent('certificate');  |
|                                        |                                                                      |
+----------------------------------------+----------------------------------------------------------------------+

``hasLocation()``
.................

Check if Location object with given name is configured.

+----------------------------------------+----------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                     |
+----------------------------------------+----------------------------------------------------------------------+
| **Returns**                            | ``boolean``                                                          |
+----------------------------------------+----------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                  |
|                                        |                                                                      |
|                                        |     $hasHomepage = $namedObjectProvider->hasLocation('homepage');    |
|                                        |                                                                      |
+----------------------------------------+----------------------------------------------------------------------+

``getLocation()``
.................

Get Location object by its name.

+----------------------------------------+----------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                     |
+----------------------------------------+----------------------------------------------------------------------+
| **Returns**                            | :ref:`Location object<location_object>`                              |
+----------------------------------------+----------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                  |
|                                        |                                                                      |
|                                        |     $homepage = $namedObjectProvider->getLocation('homepage');       |
|                                        |                                                                      |
+----------------------------------------+----------------------------------------------------------------------+

``hasTag()``
............

Check if Tag object with given name is configured.

+----------------------------------------+----------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                     |
+----------------------------------------+----------------------------------------------------------------------+
| **Returns**                            | ``boolean``                                                          |
+----------------------------------------+----------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                  |
|                                        |                                                                      |
|                                        |     $hasColors = $namedObjectProvider->hasTag('colors');             |
|                                        |                                                                      |
+----------------------------------------+----------------------------------------------------------------------+

``getTag()``
.................

Get Tag object by its name.

+----------------------------------------+----------------------------------------------------------------------+
| **Parameters**                         | ``string $name``                                                     |
+----------------------------------------+----------------------------------------------------------------------+
| **Returns**                            | Tag object                                                           |
+----------------------------------------+----------------------------------------------------------------------+
| **Example**                            | .. code-block:: php                                                  |
|                                        |                                                                      |
|                                        |     $colors = $namedObjectProvider->getTag('colors');                |
|                                        |                                                                      |
+----------------------------------------+----------------------------------------------------------------------+
