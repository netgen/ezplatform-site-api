Tag field Content relations Query Type
================================================================================

This Query Type is used to build queries that fetch Content tag field relations from selected tag
fields of a given Content.

.. hint::

    Tag field Content relations are Content items tagged with a tag contained in a tag field of a
    given Content.

.. hint::

    This query type assumes `Netgen's TagsBundle`_ is used for tagging functionality.

+-------------+------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Relations/TagFields``                                                  |
+-------------+------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                             |
| conditions  | - `exclude_self`_                                                                        |
|             | - `relation_field`_                                                                      |
+-------------+------------------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                                        |
| Content     | - `field`_                                                                               |
| conditions  | - `is_field_empty`_                                                                      |
|             | - `creation_date`_                                                                       |
|             | - `modification_date`_                                                                   |
|             | - `section`_                                                                             |
|             | - `state`_                                                                               |
+-------------+------------------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                               |
| query       | - `offset`_                                                                              |
| parameters  | - `sort`_                                                                                |
+-------------+------------------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Your project is a web shop, where Content of type ``product`` is tagged with tags that define
product's market. Specific tag field named ``market`` is used for that. For example, you could have
a wireless keyboard product tagged with market tag ``components``. Various other Content is also
tagged with that tag, for example we could have files and articles using that same tag.

On the full view for Content of type ``product``, fetch articles from the same market, sort them
by their publication date and paginate them by 10 per page using URL query parameter ``page``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        product:
                            template: '@ezdesign/content/full/product.html.twig'
                            match:
                                Identifier\ContentType: product
                            queries:
                                market_articles:
                                    query_type: SiteAPI:Content/Relations/TagFields
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        relation_field: market
                                        content_type: article
                                        sort: published desc

.. code-block:: twig

    {% set articles = ng_query( 'market_articles' ) %}

    <h3>Related market articles</h3>

    <ul>
    {% for article in articles %}
        <li>{{ article.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( articles, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``content``
~~~~~~~~~~~

Defines the source (from) relation Content, which is the one containing tag fields.

.. note::

  This condition is required. It's also automatically set to the ``Content`` instance resolved by
  the view builder if the query is defined in the view builder configuration.

- **value type**: ``Content``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

Examples:

.. code-block:: yaml

    # this is also automatically set when using from view builder configuration
    location: '@=content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's Content
    location: '@=content.mainLocation.parent.content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's parent Location's Content
    location: '@=content.mainLocation.parent.parent.content'

``exclude_self``
~~~~~~~~~~~~~~~~

Defines whether to include Content defined by the ``content`` condition in the result set.
If ``null`` is used as a value, the condition won't be added.

- **value type**: ``boolean``, `null``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``false``
- **default**: ``true``

Examples:

.. code-block:: yaml

    # do not include the source relation Content, this is also the default behaviour
    exclude_self: true

.. code-block:: yaml

    # include the source relation Content
    exclude_self: false

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

    relation_field: appellation

.. code-block:: yaml

    relation_field: [head, heart, base]

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc

.. _`Netgen's TagsBundle`: https://github.com/netgen/TagsBundle
