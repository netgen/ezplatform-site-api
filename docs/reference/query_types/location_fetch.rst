General purpose Location fetch
================================================================================

This Query Type is used to build general purpose Location queries.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Fetch``                                                   |
+-------------+------------------------------------------------------------------------------+
| Inherited   | - `depth`_                                                                   |
| Location    | - `main`_                                                                    |
| conditions  | - `parent_location_id`_                                                      |
|             | - `priority`_                                                                |
|             | - `subtree`_                                                                 |
|             | - `visible`_                                                                 |
+-------------+------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                            |
| Content     | - `field`_                                                                   |
| conditions  | - `is_field_empty`_                                                          |
|             | - `creation_date`_                                                           |
|             | - `modification_date`_                                                       |
|             | - `section`_                                                                 |
|             | - `state`_                                                                   |
+-------------+------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                   |
| query       | - `offset`_                                                                  |
| parameters  | - `sort`_                                                                    |
+-------------+------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/depth.rst.inc
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/parent_location_id.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/subtree.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
