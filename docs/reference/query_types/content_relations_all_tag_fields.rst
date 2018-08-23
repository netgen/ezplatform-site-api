All tag fields Content relations Query Type
================================================================================

This Query Type is used to build queries that fetch Content tag field relations from all tag fields of a given Content.

.. hint:: Tag field Content relations are Content items tagged with a tag contained in the tag fields of a given Content.

+-------------+---------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Relations/AllTagFields``                                                  |
+-------------+---------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                                |
| conditions  | - `exclude_self`_                                                                           |
+-------------+---------------------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                                           |
| Content     | - `field`_                                                                                  |
| conditions  | - `publication_date`_                                                                       |
|             | - `section`_                                                                                |
|             | - `state`_                                                                                  |
+-------------+---------------------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                                  |
| query       | - `offset`_                                                                                 |
| parameters  | - `sort`_                                                                                   |
+-------------+---------------------------------------------------------------------------------------------+

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Content\\Relations\\AllTagFields` |
.. +-------------+---------------------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Own conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/query_type/content.rst.inc
.. include:: /reference/query_types/parameters/query_type/exclude_self.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
