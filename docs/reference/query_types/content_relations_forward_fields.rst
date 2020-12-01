Forward field Content relations Query Type
================================================================================

This Query Type is used to build queries that fetch Content that is related to from relation type
fields of the given Content.

+-------------+----------------------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Content/Relations/ForwardFields``                                                  |
+-------------+----------------------------------------------------------------------------------------------+
| Own         | - `content`_                                                                                 |
| conditions  | - `relation_field`_                                                                          |
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

Content of type ``blog_post`` has relation field ``images`` which is used to define relations to
``image`` type Content. On full view for ``blog_post`` fetch 10 related images sorted by name and
paginate them by 10 per page using URL query parameter ``page``.

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        blog_post:
                            template: '@ezdesign/content/full/blog_post.html.twig'
                            match:
                                Identifier\ContentType: blog_post
                            queries:
                                related_images:
                                    query_type: SiteAPI:Content/Relations/ForwardFields
                                    max_per_page: 10
                                    page: 1
                                    parameters:
                                        relation_field: images
                                        content_type: image
                                        sort: name

.. code-block:: twig

    <h3>Related images</h3>

    <ul>
    {% for image in ng_query( 'related_images' ) %}
        <li>
            {{ ng_image_alias( image.fields.image, 'gallery' ) }}
        </li>
    {% endfor %}
    </ul>

    {{ pagerfanta( documents, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``content``
~~~~~~~~~~~

Defines the source (from) relation Content, which is the one containing relation type fields.

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
    content: '@=content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's Content
    content: '@=content.mainLocation.parent.content'

.. code-block:: yaml

    # fetch relations from Content's main Location parent Location's parent Location's Content
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

    relation_field: appellation

.. code-block:: yaml

    relation_field: [head, heart, base]

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
