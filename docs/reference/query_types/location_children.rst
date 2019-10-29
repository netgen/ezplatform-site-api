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

On full view for ``folder`` type Location fetch folder's children Locations of the type
``document`` that are in ``restricted`` Section, sort them by priority descending and paginate them
by 10 per page using URL query parameter ``page``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        folder:
                            template: '@ezdesign/content/full/folder.html.twig'
                            match:
                                Identifier\ContentType: folder
                            queries:
                                children_documents:
                                    query_type: SiteAPI:Location/Children
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        content_type: document
                                        section: restricted
                                        sort: priority desc

.. code-block:: twig

    {% set documents = ng_query( 'children_documents' ) %}

    <h3>Documents in this folder</h3>

    <ul>
    {% for document in documents %}
        <li>{{ document.content.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( documents, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``location``
~~~~~~~~~~~~

Defines the parent Location for children Locations.

.. note::

  This condition is required. It's also automatically set to the ``Location`` instance resolved by
  the view builder if the query is defined in the view builder configuration.

- **value type**: ``Location``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

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

Common query parameters
--------------------------------------------------------------------------------

.. include:: /reference/query_types/parameters/common/query/limit.rst.inc
.. include:: /reference/query_types/parameters/common/query/offset.rst.inc

.. include:: /reference/query_types/parameters/common/query/sort/title.rst.inc

- **value type**: ``string``, ``SortClause``
- **value format**: ``single``, ``array``
- **required**: ``false``
- **default**: as is defined on the parent Location (see `location`_  parameter)

.. include:: /reference/query_types/parameters/common/query/sort/body.rst.inc
