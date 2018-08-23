Tag field Content relations Query Type
================================================================================

This Query Type is used to build queries that fetch Content tag field relations from selected tag fields of a given Content.

.. hint:: Tag field Content relations are Content items tagged with a tag contained in the tag fields of a given Content.

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
.. include:: /reference/query_types/parameters/query_type/content.rst.inc
.. include:: /reference/query_types/parameters/query_type/exclude_self.rst.inc
.. include:: /reference/query_types/parameters/query_type/relation_field.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
