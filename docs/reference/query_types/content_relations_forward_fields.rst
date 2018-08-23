Forward field Content relations Query Type
================================================================================

This Query Type is used to build ....

+-------------+----------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Relations/ForwardFields``                                                  |
+-------------+----------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                                 |
| conditions  | - `relation_field`_                                                                          |
+-------------+----------------------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                                            |
| Content     | - `field`_                                                                                   |
| conditions  | - `publication_date`_                                                                        |
|             | - `section`_                                                                                 |
|             | - `state`_                                                                                   |
+-------------+----------------------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                                   |
| query       | - `offset`_                                                                                  |
| parameters  | - `sort`_                                                                                    |
+-------------+----------------------------------------------------------------------------------------------+

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Content\\Relations\\ForwardFields` |
.. +-------------+----------------------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Own conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/query_type/content.rst.inc
.. include:: /reference/query_types/parameters/query_type/relation_field.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
