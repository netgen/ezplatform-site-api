Reverse field Location relations Query Type
================================================================================

This Query Type is used to build queries that fetch Locations with Content relations to the given
Content from its relation type fields.

.. note::

    Only visible main Locations of the related Content will be used.

+-------------+----------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Relations/ReverseFields``                                                 |
+-------------+----------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                                 |
| conditions  | - `relation_field`_                                                                          |
+-------------+----------------------------------------------------------------------------------------------+
| Inherited   | - `main`_                                                                                    |
| Location    | - `priority`_                                                                                |
| conditions  | - `visible`_                                                                                 |
+-------------+----------------------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                                            |
| Content     | - `field`_                                                                                   |
| conditions  | - `is_field_empty`_                                                                          |
|             | - `creation_date`_                                                                           |
|             | - `modification_date`_                                                                       |
|             | - `section`_                                                                                 |
|             | - `state`_                                                                                   |
+-------------+----------------------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                                   |
| query       | - `offset`_                                                                                  |
| parameters  | - `sort`_                                                                                    |
+-------------+----------------------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Content of type ``article`` has relation field ``authors`` which is used to define relations to
``author`` type Content. On full view for ``author`` fetch all articles authored by that author,
sort them by title and paginate them by 10 per page using URL query parameter ``page``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        author:
                            template: '@ezdesign/content/full/author.html.twig'
                            match:
                                Identifier\ContentType: author
                            queries:
                                authored_articles:
                                    query_type: SiteAPI:Location/Relations/ReverseFields
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        relation_field: authors
                                        content_type: article
                                        sort: field/article/title asc

.. code-block:: twig

    <h3>Author's articles</h3>

    <ul>
    {% for article in ng_query( 'authored_articles' ) %}
        <li>{{ article.content.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( children, 'twitter_bootstrap' ) }}

Own parameters
--------------------------------------------------------------------------------

``content``
~~~~~~~~~~~

Defines the destination (to) relation Content.

.. note::

  This condition is required. It's also automatically set to the ``Content`` instance resolved by
  the view builder if the query is defined in the view builder configuration.

.. note:: Since this is about **reverse** relations, Content defined by this condition is **not**
          the one containing relation type fields referenced by ``relation_field``. It's the one
          receiving relations from Content containing those fields.

- **value type**: ``Content``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

Examples:

.. code-block:: yaml

    # this is also automatically set when using from view builder configuration
    content: '@=content'

.. code-block:: yaml

    # fetch relations to Content's main Location parent Location's Content
    content: '@=content.mainLocation.parent.content'

.. code-block:: yaml

    # fetch relations to Content's main Location parent Location's parent Location's Content
    content: '@=content.mainLocation.parent.parent.content'

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

    relation_field: authors

.. code-block:: yaml

    relation_field: [color, size]

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/main/title.rst.inc

Defines whether returned Locations are main Locations or not.
Use ``true`` to get main Locations (which is also the default behaviour), ``false`` to get non-main Locations and ``null`` to get both.

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: ``true``

.. include:: /reference/query_types/parameters/common/location/main/examples.rst.inc

.. include:: /reference/query_types/parameters/common/location/priority.rst.inc

.. include:: /reference/query_types/parameters/common/location/visible/title.rst.inc

Defines whether returned Locations are main Locations or not.
Use ``true`` to get main Locations (which is also the default behaviour), ``false`` to get non-main Locations and ``null`` to get both.

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: ``true``

.. include:: /reference/query_types/parameters/common/location/visible/examples.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
