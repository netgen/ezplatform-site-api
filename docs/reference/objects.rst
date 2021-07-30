Objects
=======

Site API comes with its own set of entities and values. These are similar, but still different from
their counterparts in eZ Platform's Repository API. Main benefits they provide over them are:

- Content is available in a single translation, this voids the need for various helper services
- Additional properties otherwise available only through separate entities (like ContentType
  identifier, FieldType identifier and others)
- Additional properties and methods that enable simple traversal and filtering of the content model
  (relations, parent, siblings, children)

.. note::

    Note that content traversal that is achievable through the objects is not complete. It aims to
    cover only the most common use cases. For more complex use cases :doc:`Query Types </reference/query_types>`
    should be used.

.. note::

    In Twig templates methods beginning with ``get`` and ``is`` are also available with that prefix
    removed. Also, parentheses can be omitted if there are no required arguments.

    For example, method ``field.isEmpty()`` is also available as ``field.empty()`` or just
    ``field.empty``, and method ``content.getLocations()`` is available as ``content.locations()``
    or just ``content.locations``.

**Content on this page:**

.. contents::
    :depth: 3
    :local:

.. _content_object:

``Content``
-----------

The first difference from Repository Content is that it exist it a single translation only, meaning
it contains the fields for only one translation. That will always be the translation to be rendered
on the siteaccess. You won't need to choose the field in the correct translation, manually or
through some kind of helper service. The Content's single translation is always the correct one.

Content fields are lazy-loaded, which means they are initially not loaded, but will be transparently
loaded at the point you access them. This voids the need to have separate, lightweight version of
Content (ContentInfo plays this role in Repository API). It also provides you with some additional
properties and methods.

Example usage from Twig:

.. code-block:: twig

    <h1>{{ content.name }}</h1>
    <h2>Parent name: {{ content.mainLocation.parent.content.name }}</h2>
    <h3>Number of Locations: {{ content.locations|length }}</h3>

    {% for field in content.fields %}
        {% if not field.empty %}
            {{ ng_render_field(field) }}
        {% endif %}
    {% endfor %}

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``hasField``
............

Check if Content has a `Field`_ with the given ``$identifier``.

+----------------------------------------+-----------------------------------------------------------------------------+
| **Parameters**                         | ``string $identifier``                                                      |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Returns**                            | ``bool``                                                                    |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                         |
|                                        |                                                                             |
|                                        |     if ($content->hasField('title')) {                                      |
|                                        |         // ...                                                              |
|                                        |     }                                                                       |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                        |
|                                        |                                                                             |
|                                        |     {% if content.hasField('title') %}                                      |
|                                        |         ...                                                                 |
|                                        |     {% endif %}                                                             |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+

``getField``
............

Get the `Field`_ with the given ``$identifier``.

.. note::

    This method can return ``null`` if Field with the given ``$identifier`` doesn't exist.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $identifier``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | `Field`_ instance or ``null``                                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $field = $content->getField('title');                                          |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set field = content.field('title') %}                                       |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``hasFieldById``
................

Check if Content has a `Field`_ with the given ``$id``.

+----------------------------------------+-----------------------------------------------------------------------------+
| **Parameters**                         | ``int|string $id``                                                          |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Returns**                            | ``bool``                                                                    |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                         |
|                                        |                                                                             |
|                                        |     $content->hasFieldById(42);                                             |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                        |
|                                        |                                                                             |
|                                        |     {{ content.hasFieldById(42) }}                                          |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+

``getFieldById``
................

Get the `Field`_ with the given ``$id``.

.. note::

    This method can return ``null`` if Field with the given ``$id`` doesn't exist.

+----------------------------------------+-----------------------------------------------------------------------------+
| **Parameters**                         | ``string $id``                                                              |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Returns**                            | `Field`_ instance or ``null``                                               |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                         |
|                                        |                                                                             |
|                                        |     $field = $content->getFieldById(42);                                    |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                        |
|                                        |                                                                             |
|                                        |     {% set field = content.fieldById(42) %}                                 |
|                                        |                                                                             |
+----------------------------------------+-----------------------------------------------------------------------------+

``getFieldValue``
.................

Get the value of the `Field`_ with the given ``$identifier``.

.. note::

    This method can return ``null`` if Field with the given ``$identifier`` doesn't exist.

.. note::

    Returned value object depends of the FieldType. Best way to learn about the specific value
    format is reading the official `FieldType reference <https://doc.ez.no/display/EZP/FieldTypes+reference>`_ documentation,
    or looking directly at code (for example `the code of TextLine Value <https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/Core/FieldType/TextLine/Value.php>`_).

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $identifier``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Value instance of the `Field`_ or ``null``                                         |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $value = $content->getFieldValue('title');                                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set value = content.fieldValue('title') %}                                  |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getFieldValueById``
.....................

Get the value of the `Field`_ with the given ``$id``.

.. note::

    This method can return ``null`` if Field with the given ``$id`` doesn't exist.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $id``                                                                     |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Value instance of the `Field`_ or ``null``                                         |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $value = $content->getFieldValueById(42);                                      |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set value = content.fieldValueById(42) %}                                   |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getLocations``
................

Used to get Content's Locations, limited by the ``$limit``. Locations will be sorted their path
string (a string with materialized IDs, e.g. ``/1/2/45/67/``).

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``int $limit = 25``                                                                |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of Content's `Locations`__                                                |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | Location's path string (e.g. ``/1/2/45/67/``)                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $locations = $content->locations(10);                                          |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set locations = content.locations %}                                        |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

__ Location_

``filterLocations``
...................

List a slice of Content's Locations, by the ``$maxPerPage`` and ``$currentPage``. Locations will be
sorted their path string (a string with materialized IDs, e.g. ``/1/2/45/67/``).

+----------------------------------------+--------------------------------------------------------------+
| **Parameters**                         | 1. ``int $maxPerPage = 25``                                  |
|                                        | 2. ``int $currentPage = 1``                                  |
+----------------------------------------+--------------------------------------------------------------+
| **Returns**                            | Pagerfanta instance with a slice of Content's `Locations`__  |
+----------------------------------------+--------------------------------------------------------------+
| **Sorting method**                     | Location's path string (e.g. ``/1/2/45/67/``)                |
+----------------------------------------+--------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                          |
|                                        |                                                              |
|                                        |     $locations = $content->filterLocations(10, 2);           |
|                                        |                                                              |
+----------------------------------------+--------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                         |
|                                        |                                                              |
|                                        |     {% set locations = content.filterLocations(10, 2) %}     |
|                                        |                                                              |
+----------------------------------------+--------------------------------------------------------------+

__ Location_

``getFieldRelation``
....................

Used to get a single field relation Content from the `Field`_ with the given ``$identifier``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $identifier``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Related `Content`_ or ``null`` if the relation does not exist                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relation = $content->getFieldRelation('author');                              |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relation = content.fieldRelation('author') %}                           |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getFieldRelations``
.....................

Used to get ``$limit`` field relation Content items from the `Field`_ with the given ``$identifier``. Relations
will be sorted as is defined by the relation field.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string $identifier``                                                          |
|                                        | 2. ``int $limit = 25``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of related `Content`_ items                                               |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | Sorted as is defined by the relation `Field`_                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relations = $content->getFieldRelations('images', 10);                        |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relations = content.fieldRelations('images') %}                         |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``filterFieldRelations``
........................

Used to filter field relation Content items from the `Field`_ with the given ``$identifier``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string $identifier``                                                          |
|                                        | 2. ``array $contentTypeIdentifiers = []``                                          |
|                                        | 3. ``int $maxPerPage = 25``                                                        |
|                                        | 4. ``int $currentPage = 1``                                                        |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Pagerfanta instance with related `Content`_ items                                  |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relations = $content->filterFieldRelations(                                   |
|                                        |         'related_items',                                                           |
|                                        |         ['images', 'videos'],                                                      |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relations = content.filterFieldRelations(                               |
|                                        |         'related_items'                                                            |
|                                        |         ['images', 'videos']                                                       |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     ) %}                                                                           |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getFieldRelationLocation``
............................

Used to get a single field relation Location from the `Field`_ with the given ``$identifier``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $identifier``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Related `Location`_ or ``null`` if the relation does not exist                     |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relation = $content->getFieldRelationLocation('author');                      |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relation = content.fieldRelationLocation('author') %}                   |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getFieldRelationLocations``
.............................

Used to get ``$limit`` field relation Locations from the `Field`_ with the given ``$identifier``. Relations
will be sorted as is defined by the relation field.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string $identifier``                                                          |
|                                        | 2. ``int $limit = 25``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of related `Location`_ items                                              |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | Sorted as is defined by the relation `Field`_                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relations = $content->getFieldRelationLocations('images', 10);                |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relations = content.fieldRelationLocations('images') %}                 |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``filterFieldRelationLocations``
................................

Used to filter field relation Locations from the `Field`_ with the given ``$identifier``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``string $identifier``                                                          |
|                                        | 2. ``array $contentTypeIdentifiers = []``                                          |
|                                        | 3. ``int $maxPerPage = 25``                                                        |
|                                        | 4. ``int $currentPage = 1``                                                        |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Pagerfanta instance with related `Location`_ items                                 |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $relations = $content->filterFieldRelationLocations(                           |
|                                        |         'related_items',                                                           |
|                                        |         ['images', 'videos'],                                                      |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     );                                                                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relations = content.filterFieldRelationLocations(                       |
|                                        |         'related_items'                                                            |
|                                        |         ['images', 'videos']                                                       |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     ) %}                                                                           |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

Properties
~~~~~~~~~~

+---------------------+---------------------+-----------------------------------------------------------------------------+
| Name                | Type                | Description                                                                 |
+=====================+=====================+=============================================================================+
| ``$id``             | ``string|int``      | ID                                                                          |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$mainLocationId`` | ``string|int|null`` | Optional main `Location`_ ID                                                |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$name``           | ``string``          | Name                                                                        |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$languageCode``   | ``string``          | Translation language code                                                   |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$contentInfo``    | `ContentInfo`_      | ContentInfo object                                                          |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$fields``         | ``Field[]``         | | An array of `Field`_ instances, which can be accessed                     |
|                     |                     | | in two different ways:                                                    |
|                     |                     |                                                                             |
|                     |                     | .. code-block:: twig                                                        |
|                     |                     |                                                                             |
|                     |                     |     {% set field = content.fields.title %}                                  |
|                     |                     |     {% set field = content.fields['title'] %}                               |
|                     |                     |                                                                             |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$mainLocation``   | `Location`_         | Optional Location object                                                    |
+---------------------+---------------------+-----------------------------------------------------------------------------+
| ``$owner``          | `Content`_          | Optional owner user's Content object                                        |
+---------------------+---------------------+-----------------------------------------------------------------------------+

``ContentInfo``
---------------

Site ``ContentInfo`` object is similar to the Repository ContentInfo, additionally providing access
to

Properties
~~~~~~~~~~

+-----------------------------+----------------+----------------------------------------------------------+
| Name                        | Type           | Description                                              |
+=============================+================+==========================================================+
| ``$id``                     | ``string|int`` | ID of the Content                                        |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$contentTypeId``          | ``string|int`` | ID of the ContentType                                    |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$sectionId``              | ``string|int`` | ID of the Section                                        |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$currentVersionNo``       | ``int``        | Current version number                                   |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$published``              | ``bool``       | Indicates that the Content is published                  |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$ownerId``                | ``string|int`` | ID of the owner user Content                             |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$modificationDate``       | ``\DateTime``  | | Modification date                                      |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$publishedDate``          | ``\DateTime``  | Publication date                                         |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$alwaysAvailable``        | ``bool``       | | Indicates that the Content is always available in its  |
|                             |                | | main translation                                       |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$remoteId``               | ``string``     | Remote ID of the Content                                 |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$mainLanguageCode``       | ``string``     | Main translation language code                           |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$mainLocationId``         | ``string|int`` | ID of the main Location                                  |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$name``                   | ``string``     | Content's name                                           |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$languageCode``           | ``string``     | Language code of Content's translation                   |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$contentTypeIdentifier``  | ``string``     | Identifier of the Content Type                           |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$contentTypeName``        | ``string``     | Name of the Content Type                                 |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$contentTypeDescription`` | ``string``     | Description of the Content Type                          |
+-----------------------------+----------------+----------------------------------------------------------+
| ``$mainLocation``           | `Location`_    | Content's main Location object                           |
+-----------------------------+----------------+----------------------------------------------------------+

``Field``
---------

Site ``Field`` object is similar to the Repository Field, additionally providing access to the
field's `Content`_ and properties that are otherwise available only through the corresponding
FieldDefinition object: name, description and FieldType identifier.

Methods
~~~~~~~

``isEmpty``
...........

Checks if the field's value is empty.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | None                                                                               |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | ``bool``                                                                           |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     if ($content->getField('title')->isEmpty()) {                                  |
|                                        |         // ...                                                                     |
|                                        |     }                                                                              |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% if content.fields.title.empty %}                                            |
|                                        |         ...                                                                        |
|                                        |     {% endif %}                                                                    |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``isSurrogate``
...............

Checks if the field is of ``ngsurrogate`` type, returned when nonexistent field is requested from Content.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | None                                                                               |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | ``bool``                                                                           |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     if ($content->getField('title')->isSurrogate()) {                              |
|                                        |         // ...                                                                     |
|                                        |     }                                                                              |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% if content.fields.title.surrogate %}                                        |
|                                        |         ...                                                                        |
|                                        |     {% endif %}                                                                    |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

Properties
~~~~~~~~~~

+--------------------------+----------------+--------------------------------------------------------------+
| Name                     | Type           | Description                                                  |
+==========================+================+==============================================================+
| ``$id``                  | ``string|int`` | ID of the Field                                              |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$fieldDefIdentifier``  | ``string``     | Identifier (FieldDefinition identifier, e.g. ``title``)      |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$value``               | Value object   | Value object                                                 |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$languageCode``        | ``string``     | Translation language code                                    |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$fieldTypeIdentifier`` | ``string``     | FieldType identifier (e.g. ``ezstring``)                     |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$name``                | ``string``     | Name of the Field                                            |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$description``         | ``string``     | Description of the Field                                     |
+--------------------------+----------------+--------------------------------------------------------------+
| ``$content``             | `Content`_     | Content object                                               |
+--------------------------+----------------+--------------------------------------------------------------+

.. _location_object:

``Location``
------------

Site ``Location`` object is similar to the Repository Location, additionally providing methods and
properties that enable simple traversal and filtering of the Location tree (siblings, children,
parent, ancestors etc).

Methods
~~~~~~~

.. contents::
    :depth: 1
    :local:

``getChildren``
...............

List children Locations.

Children will be sorted as is defined by their parent Location, which is the Location the method is
called on. The single optional parameter of this method is ``$limit``, which limits the number of
children returned and defaults to ``25``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $limit = 25``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of first ``$limit`` children Locations                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | As is defined by the Location                                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $children = $location->getChildren(10);                                        |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set children = location.children(10) %}                                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getFirstChild``
.................

Get the first child of the Location.

First child will be returned from children sorted as is defined by their parent Location, which is
the Location the method is called on. The single optional parameter of this method is
``$contentTypeIdentifier``, which returned Location must match.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``?string $contentTypeIdentifier = null``                                          |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | First child `Location`_ or ``null`` if there are no children Locations             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | As is defined by the Location                                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $firstChild = $location->getFirstChild('article');                             |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set first_child = location.firstChild('article') %}                         |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``filterChildren``
..................

Filter and paginate children Locations.

This enables filtering of the children by their ContentType with ``$contentTypeIdentifiers``
parameter and pagination using ``$maxPerPage`` and ``$currentPage`` parameters. The method returns
a Pagerfanta instance.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``array $contentTypeIdentifiers = []``                                          |
|                                        | 2. ``int $maxPerPage = 25``                                                        |
|                                        | 3. ``int $currentPage = 1``                                                        |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Pagerfanta instance with a slice of children Locations                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | As is defined by the Location                                                      |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $children = $content->filterChildren(['articles'], 10, 2);                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set relation = content.filterChildren(                                      |
|                                        |         ['articles'],                                                              |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     ) %}                                                                           |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``getSiblings``
...............

List sibling Locations.

Siblings will be sorted as is defined by their parent Location, which is the parent Location of the
Location the method is called on. The single optional parameter of this method is ``$limit``, which
limits the number of siblings returned and defaults to ``25``.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | ``string $limit = 25``                                                             |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | An array of first ``$limit`` sibling Locations                                     |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | As is defined by the parent Location                                               |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $siblings = $location->getSiblings(10);                                        |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set siblings = location.siblings(10) %}                                     |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

``filterSiblings``
..................

Filter and paginate sibling Locations.

This enables filtering of the siblings by their ContentType with ``$contentTypeIdentifiers``
parameter and pagination using ``$maxPerPage`` and ``$currentPage`` parameters. The method returns
a Pagerfanta instance.

+----------------------------------------+------------------------------------------------------------------------------------+
| **Parameters**                         | 1. ``array $contentTypeIdentifiers = []``                                          |
|                                        | 2. ``int $maxPerPage = 25``                                                        |
|                                        | 3. ``int $currentPage = 1``                                                        |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Returns**                            | Pagerfanta instance with a slice of filtered sibling Locations                     |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Sorting method**                     | As is defined by the parent Location                                               |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in PHP**                     | .. code-block:: php                                                                |
|                                        |                                                                                    |
|                                        |     $siblings = $location->filterSiblings(['articles'], 10, 2);                    |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+
| **Example in Twig**                    | .. code-block:: twig                                                               |
|                                        |                                                                                    |
|                                        |     {% set siblings = location.filterSiblings(                                     |
|                                        |         ['articles'],                                                              |
|                                        |         10,                                                                        |
|                                        |         2                                                                          |
|                                        |     ) %}                                                                           |
|                                        |                                                                                    |
+----------------------------------------+------------------------------------------------------------------------------------+

Properties
~~~~~~~~~~

+-----------------------+----------------+---------------------------------------------------------------------------+
| Name                  | Type           | Description                                                               |
+=======================+================+===========================================================================+
| ``$id``               | ``string|int`` | ID of the Location                                                        |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$status``           | ``int``        | Constant defining status (published or draft)                             |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$priority``         | ``int``        | Priority                                                                  |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$hidden``           | ``bool``       | Own hidden state                                                          |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$invisible``        | ``bool``       | Invisibility state (hidden by itself or by an ancestor)                   |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$remoteId``         | ``string``     | Remote ID                                                                 |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$parentLocationId`` | ``string|int`` | Parent Location ID                                                        |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$pathString``       | ``string``     | Path with materialized IDs (``/1/2/42/56/``)                              |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$path``             | ``int[]``      | An array with materialized IDs (``[1, 2, 42, 56]``)                       |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$depth``            | ``int``        | Depth in the Location tree                                                |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$sortField``        | ``int``        | Constant defining field for sorting children Locations                    |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$sortOrder``        | ``int``        | Constant defining sort order for children Locations                       |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$contentId``        | ``string|int`` | ID of the Content                                                         |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$contentInfo``      | `ContentInfo`_ | ContentInfo object                                                        |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$parent``           | `Location`_    | Parent Location object (lazy loaded)                                      |
+-----------------------+----------------+---------------------------------------------------------------------------+
| ``$content``          | `Content`_     | Content object (lazy loaded)                                              |
+-----------------------+----------------+---------------------------------------------------------------------------+
