Query Types
===========

Site API Query Types expand upon Query Type feature from eZ Publish Kernel, using the same basic interfaces. That will enable using your existing Query Types, but how Site API integrates them with the rest of the system differs from eZ Publish Kernel.

Built-in Query Types
--------------------

A number of generic Query Types is provided out of the box. We can separate these into three groups:

General purpose
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/general_purpose_query_types.rst.inc

Content relations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/content_relations_query_types.rst.inc

Location hierarchy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/location_query_types.rst.inc

Query Type configuration
--------------------------------------------------------------------------------

Query Types have their own semantic configuration under ``queries`` key in configuration for a particular Content view. Under this key separate queries are defined under their own identifier keys, which are later used to reference the configured query from the Twig templates.

Available parameters and their default values are:

- ``query_type`` - identifies the Query Type to be used
- ``named_query`` - identifies named query to be used
- ``max_per_page: 25`` - pagination parameter for maximum number of items per page
- ``page: 1`` - pagination parameter for current page
- ``use_filter: true`` - whether to use ``FilterService`` or ``FindService`` for executing the query
- ``parameters: []`` - contains the actual Query Type parameters

Parameters ``query_type`` and ``named_query`` are mutually exclusive, you are allowed to set only one or the other. But they are also mandatory - you will have to set one of them.

Example below shows how described configuration looks in practice:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        category:
                            template: '@ezdesign/content/full/category.html.twig'
                            match:
                                Identifier\ContentType: 'category'
                            queries:
                                children:
                                    query_type: 'SiteAPI:Location/Children'
                                    max_per_page: 10
                                    page: 1
                                    parameters:
                                        content_type: 'article'
                                        sort: 'published desc'
                                related_images:
                                    query_type: 'SiteAPI:Content/Relations/ForwardFields'
                                    max_per_page: 10
                                    page: 1
                                    parameters:
                                        content_type: 'article'
                                        sort: 'published desc'
                            params:
                                ...


.. note:: You can define unlimited number of queries on any controller.

Named Query Type configuration
------------------------------

As hinted above with ``named_query`` parameter, it is possible to define "named queries", which can be referenced in query configuration for a particular content view. They are configured under ``ng_named_query``, which is a top section of a siteaccess configuration, on the same level as ``ng_content_view``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_named_query:
                    children_named_query:
                        query_type: 'SiteAPI:Location/Children'
                        max_per_page: 10
                        page: 1
                        parameters:
                            content_type: 'article'
                            sort: 'published desc'
                ngcontent_view:
                    full:
                        category:
                            template: '@ezdesign/content/full/category.html.twig'
                            match:
                                Identifier\ContentType: 'category'
                            queries:
                                children: 'children_named_query'
                                children_5_per_page:
                                    named_query: 'children_named_query'
                                    max_per_page: 5
                                images:
                                    named_query: 'children_named_query'
                                    parameters:
                                        content_type: 'image'
                            params:
                                ...

.. note:: You can override some of the parameters from the referenced named query.

You can notice that there are two ways of referencing a named query. In case when there are no other parameters, you can do it directly like this:.

.. code-block:: yaml

    queries:
        children: 'children_named_query'

The example above is really just a shortcut to the example below:

.. code-block:: yaml

    queries:
        children:
            named_query: 'children_named_query'

You can also notice that it's possible to override parameters from the referenced named query. This is limited to first level keys from the main configuration and also first level keys under the ``parameters`` key.

Language expressions
--------------------------------------------------------------------------------

TODO

Accessing the configured queries from Twig
--------------------------------------------------------------------------------

Configured queries will be available in Twig templates, through ``ng_query`` or ``ng_raw_query``. The difference it that the former will return a ``Pagerfanta`` instance, while the latter will return an instance of ``SerachResult``. That also means ``ng_query`` will use ``max_per_page`` and ``page`` parameters to configure the pager, while ``ng_raw_query`` ignores them and executes the configured query directly.

.. note:: Queries are only executed as you access them through ``ng_query`` or ``ng_raw_query``. If you don't call those functions on any of the configured queries, none of them will be executed.

Both ``ng_query`` and ``ng_raw_query`` accept a single argument. This is the identifier of the query, which is the key under the ``queries`` section, under which the query is configured.

Example usage of ``ng_query``:

.. code-block:: twig

    {% set images = ng_query( 'images' ) %}

    <p>Total images: {{ images.nbResults }}</p>

    {% for image in images %}
        <p>{{ image.content.name }}</p>
    {% endfor %}

    {{ pagerfanta( images, 'twitter_bootstrap' ) }}

Example usage of ``ng_raw_query``:

.. code-block:: twig

    {% set searchResult = ng_raw_query( 'categories' ) %}

    {% for categoryHit in searchResult.searchHits %}
        <p>{{ categoryHit.valueObject.content.name }}: {{ categoryHit.valueObject.score }}</p>
    {% endfor %}

.. note:: You can't execute named queries. They are only available for referencing in concrete query configuration for a particular view.

.. note:: Execution of queries is **not cached**. If you call ``ng_query`` or ``ng_raw_query`` on the same query multiple times, the same query will be executed multiple times. You should store the result in a variable and reuse the variable instead.

.. hint:: If you need to access the same query result multiple times, store it in a variable and instead access that variable.
