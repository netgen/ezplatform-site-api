Tag field Content relations Query Type
================================================================================

This Query Type is used to build queries that fetch Content tag field relations from selected tag
fields of a given Content.

.. hint::

    Tag field Content relations are Content items tagged with a tag contained in a tag field of a
    given Content.

+-------------+------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Relations/TagFields``                                                  |
+-------------+------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                             |
| conditions  | - `exclude_self`_                                                                        |
|             | - `relation_field`_                                                                      |
+-------------+------------------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                                        |
| Content     | - `field`_                                                                               |
| conditions  | - `publication_date`_                                                                    |
|             | - `section`_                                                                             |
|             | - `state`_                                                                               |
+-------------+------------------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                               |
| query       | - `offset`_                                                                              |
| parameters  | - `sort`_                                                                                |
+-------------+------------------------------------------------------------------------------------------+

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Content\\Relations\\TagFields` |
.. +-------------+------------------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Own conditions
--------------------------------------------------------------------------------

``content``
~~~~~~~~~~~

Defines the source (from) relation Content, which is the one containing tag fields.

.. note:: This condition is required.

- **value type**: ``Content``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

  If used through view builder configuration, value will be automatically set to the ``Content``
  instance resolved by the view builder.

Examples:

.. code-block:: yaml

    # this is also automatically set when using from view builder configuration
    location: '@=content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's Content
    location: '@=content.mainLocation.parent.content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's parent Location's Content
    location: '@=content.mainLocation.parent.parent.content'

``exclude_self``
~~~~~~~~~~~~~~~~

Defines whether to include Content defined by the ``content`` condition in the result set.

- **value type**: ``boolean``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``false``
- **default**: ``true``

Examples:

.. code-block:: yaml

    # do not include the source relation Content, this is also the default behaviour
    exclude_self: true

.. code-block:: yaml

    # include the source relation Content
    exclude_self: false

``relation_field``
~~~~~~~~~~~~~~~~~~

Defines Content fields to take into account for determining relations.

- **value type**: ``string``
- **value format**: ``single``, ``array``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

Examples:

.. code-block:: yaml

    relation_field: appellation

.. code-block:: yaml

    relation_field: [head, heart, base]

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
