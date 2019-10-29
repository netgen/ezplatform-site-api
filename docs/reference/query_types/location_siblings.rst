Location siblings Query Type
================================================================================

This Query Type is used to build queries that fetch Location siblings.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Siblings``                                                |
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

On the full view for ``article`` type Content fetch all siblings of type ``news`` that are in
ObjectState ``review/approved``, sort them by name and paginate them by 10 per page using URL query
parameter ``page``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        article:
                            template: '@ezdesign/content/full/article.html.twig'
                            match:
                                Identifier\ContentType: article
                            queries:
                                news_siblings:
                                    query_type: SiteAPI:Location/Siblings
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        content_type: news
                                        state:
                                            review: approved
                                        sort: name

.. code-block:: twig

    {% set news_list = ng_query( 'news_siblings' ) %}

    <h3>Article's news siblings</h3>

    <ul>
    {% for news in news_list %}
        <li>{{ news.content.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( news_list, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``location``
~~~~~~~~~~~~

Defines sibling Location reference for fetching other siblings Locations.

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

    # fetch siblings of the parent Location
    location: '@=location.parent'

.. code-block:: yaml

    # fetch siblings of the parent Location's parent Location
    location: '@=location.parent.parent'

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
