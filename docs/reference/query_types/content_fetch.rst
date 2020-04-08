General purpose Content fetch
================================================================================

This Query Type is used to build general purpose Content queries.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Fetch``                                                    |
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

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Content\\Fetch`    |
.. +-------------+------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
