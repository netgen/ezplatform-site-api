Location subtree Query Type
================================================================================

This Query Type is used to build queries that fetch from the Location subtree.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Subtree``                                                 |
+-------------+------------------------------------------------------------------------------+
| Own         | - `exclude_self`_                                                            |
| conditions  | - `location`_                                                                |
|             | - `relative_depth`_                                                          |
+-------------+------------------------------------------------------------------------------+
| Inherited   | - `depth`_                                                                   |
| Location    | - `main`_                                                                    |
| conditions  | - `priority`_                                                                |
|             | - `visible`_                                                                 |
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
.. include:: /reference/query_types/parameters/query_type/exclude_self.rst.inc
.. include:: /reference/query_types/parameters/query_type/location.rst.inc
.. include:: /reference/query_types/parameters/query_type/relative_depth.rst.inc

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/depth.rst.inc
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
