Location children Query Type
================================================================================

This Query Type is used to build queries that fetch children Locations.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Children``                                                |
+-------------+------------------------------------------------------------------------------+
| Own         | - `location`_                                                                |
| conditions  |                                                                              |
+-------------+------------------------------------------------------------------------------+
| Inherited   | - `main`_                                                                    |
| Location    | - `priority`_                                                                |
| conditions  | - `visible`_                                                                 |
+-------------+------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                            |
| Content     | - `field`_                                                                   |
| conditions  | - `publication_date`_                                                        |
|             | - `section`_                                                                 |
|             | - `state`_                                                                   |
+-------------+------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                   |
| query       | - `offset`_                                                                  |
| parameters  | - `sort`_                                                                    |
+-------------+------------------------------------------------------------------------------+

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Location\\Fetch`   |
.. +-------------+------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Own conditions
--------------------------------------------------------------------------------

``location``
~~~~~~~~~~~~

Defines the parent Location for children Locations.

.. note:: This condition is required.

- **value type**: ``Location``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

  If used through view builder configuration, value will be automatically set to the ``Location`` instance resolved by the view builder.

Examples:

.. code-block:: yaml

    # this is also automatically set when using from view builder configuration
    location: '@=location'

.. code-block:: yaml

    # fetch children of the parent Location
    location: '@=location.parent'

.. code-block:: yaml

    # fetch children of the parent Location's parent Location
    location: '@=location.parent.parent'

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
