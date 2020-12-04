Query Types
===========

Site API Query Types expand upon Query Type feature from eZ Publish Kernel, using the same basic
interfaces. That will enable using your existing Query Types, but how Site API integrates them with
the rest of the system differs from eZ Publish Kernel.

**Content on this page:**

.. contents::
    :depth: 3
    :local:

Built-in Site API Query Types
--------------------------------------------------------------------------------

A number of generic Query Types is provided out of the box. We can separate these into three groups:

General purpose
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/general_purpose_query_types.rst.inc

Content relations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/content_relations_query_types.rst.inc

Location relations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/location_relations_query_types.rst.inc

Location hierarchy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. include:: /reference/query_types/location_query_types.rst.inc

Query configuration
--------------------------------------------------------------------------------

Query Types have their own semantic configuration under ``queries`` key in configuration for a
particular Content view. Under this key separate queries are defined under their own identifier
keys, which are later used to reference the configured query from the Twig templates.

Available parameters and their default values are:

- ``query_type`` - identifies the Query Type to be used
- ``named_query`` - identifies named query to be used
- ``max_per_page: 25`` - pagination parameter for maximum number of items per page
- ``page: 1`` - pagination parameter for current page
- ``use_filter: true`` - whether to use ``FilterService`` or ``FindService`` for executing the query
- ``parameters: []`` - contains the actual Query Type parameters

Parameters ``query_type`` and ``named_query`` are mutually exclusive, you are allowed to set only
one or the other. But they are also mandatory - you will have to set one of them.

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
                                        content_type: 'image'
                                        sort: 'published desc'
                            params:
                                ...


.. note:: You can define unlimited number of queries on any controller.

Named query configuration
------------------------------

As hinted above with ``named_query`` parameter, it is possible to define "named queries", which can
be referenced in query configuration for a particular content view. They are configured under
``ng_named_query``, which is a top section of a siteaccess configuration, on the same level as
``ng_content_view``:

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

You can notice that there are two ways of referencing a named query. In case when there are no other
parameters, you can do it directly like this:.

.. code-block:: yaml

    queries:
        children: 'children_named_query'

The example above is really just a shortcut to the example below:

.. code-block:: yaml

    queries:
        children:
            named_query: 'children_named_query'

You can also notice that it's possible to override parameters from the referenced named query. This
is limited to first level keys from the main configuration and also first level keys under the
``parameters`` key.

Parameters with expressions
--------------------------------------------------------------------------------

When defining parameters it's possible to use expressions. These are evaluated by Symfony's
`Expression Language <https://symfony.com/doc/current/components/expression_language.html>`_
component, whose syntax is based on Twig and is documented `here <https://symfony.com/doc/current/components/expression_language/syntax.html>`_.

Expression strings are recognized by ``@=`` prefix. Following sections describe available objects,
services and functions.

View object
~~~~~~~~~~~

Site API View object is available as ``view``. You can access any `parameters injected into it <https://doc.ez.no/display/EZP/Parameters+injection+in+content+views>`_,
for example current page value in children query:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: '@=view.getParameter("page")'
                parameters:
                    content_type: 'article'
                    sort: 'published desc'

Method ``getParameter()`` on the View object does not support default value fallback, so if the
requested parameter is not there an exception will be thrown. Function ``viewParam(name, default)``
is a wrapper around it that provides a default value fallback:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: '@=viewParam("page", 10)'
                parameters:
                    content_type: 'article'
                    sort: 'published desc'

Request object
~~~~~~~~~~~~~~

Symfony's Request object is available as ``request``. For example, you can access current page
directly from the parameter in the Request object:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: '@=request.query.get("page", 1)'
                parameters:
                    content_type: 'article'
                    sort: 'published desc'

Several functions relating to the Request object are also available. These provide access to the
Request values in a more convenient way. First of these is:

``queryParam(name, default, array allowed = null)``

This function is a shortcut to ``GET`` / query string parameters on the Request object.
Through optional third parameter ``allowed`` you can define an array of allowed values. This can be
useful when you need to limit what is being passed through the query string. For example you can
use it to limit filtering by ContentType to articles and news items:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: '@=queryParam("page", 1)'
                parameters:
                    content_type: '@=queryParam("type", "article", ["article", "news"])'
                    sort: 'published desc'

Query string parameters accessed through the Request object will always be of the ``string`` type,
which can be a problem if you need to use them for configuration that expects a different scalar type.
For that reason separate type-casting getter functions are also provided:

- ``queryParamInt(name, default, array allowed = null)``

    Performs type casting of the found value to ``integer`` type.

- ``queryParamBool(name, default, array allowed = null)``

    Performs type casting of the found value to ``boolean`` type.

- ``queryParamFloat(name, default, array allowed = null)``

    Performs type casting of the found value to ``float`` type.

- ``queryParamString(name, default, allowed = [])``

    Performs type casting of the found value to ``string`` type.

Content and Location objects
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

:ref:`Site API Content object<content_object>` is available as ``content``. For example you could
store ContentType identifier for the children in a TextLine field ``content_type`` and access it
like this:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: 1
                parameters:
                    content_type: '@=content.fields.content_type.value.text'
                    sort: 'published desc'

:ref:`Site API Location object<location_object>` is available as ``location``. In the following
example we use it to find only children of the same ContentType as the parent:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: 1
                parameters:
                    content_type: '@=location.contentInfo.contentTypeIdentifier'
                    sort: 'published desc'

Content Fields
~~~~~~~~~~~~~~

Function ``fieldValue(identifier)`` provides access to the field value object of the
:ref:`Site API Content object<content_object>`. It's a shortcut function which is identical to
``content.fields.identifier.value``:

.. code-block:: yaml

    ...
        queries:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: 10
                page: 1
                parameters:
                    content_type: '@=fieldValue("content_type").text'
                    sort: 'published desc'

Configuration
~~~~~~~~~~~~~

eZ Platform ConfigResolver service is available as ``configResolver``. Through it you can access
dynamic (per siteaccess) configuration, for example maximum items per page:

.. code-block:: yaml

    ngsite.eng.max_per_page: 10 # limit to 10 items on English siteaccess
    ngsite.jpn.max_per_page: 20 # and 20 items on Japanese siteaccess

.. code-block:: yaml

    ...
        ng_named_query:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: '@=configResolver.getParameter("max_per_page", "ngsite")'
                page: 1
                parameters:
                    content_type: 'article'
                    sort: 'published desc'

Function ``config(name, namespace = null, scope = null)`` is a shortcut to ``getParameter()`` method
of ``ConfigResolver`` service:

.. code-block:: yaml

    ngsite.eng.max_per_page: 10 # limit to 10 items on English siteaccess
    ngsite.jpn.max_per_page: 20 # and 20 items on Japanese siteaccess

.. code-block:: yaml

    ...
        ng_named_query:
            children:
                query_type: 'SiteAPI:Location/Children'
                max_per_page: '@=config("max_per_page", "ngsite")'
                page: 1
                parameters:
                    content_type: 'article'
                    sort: 'published desc'

.. _named_object_query_types:

Named Objects
~~~~~~~~~~~~~

Named objects feature provides a way to configure specific objects (``Content``, ``Location`` and
``Tag``) by name and ID, and a way to access them by name from PHP, Twig and Query Type
configuration. Site API NamedObjectProvider service is available as ``namedObject``. Its purpose is
providing access to configured named objects.

.. note::

    Configuration of named objects is documented in more detail :ref:`on the Configuration page<named_object_configuration>`.
    Usage of named objects from PHP is :ref:`documented on the Services page<named_object_php>`.

The following example shows how to configure named query that will fetch top categories (Locations
of type ``category`` found below the root Location):

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                named_objects:
                    location:
                        homepage: 2

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_named_query:
                    top_categories:
                        query_type: 'SiteAPI:Location/Children'
                        parameters:
                            location: '@=namedObject.getLocation("homepage")'
                            content_type: 'category'
                            sort: 'name desc'

Shortcut functions are available for accessing each type of named object directly:

- ``namedContent(name)``

    Provides access to named Content.

- ``namedLocation(name)``

    Provides access to named Location.

- ``namedTag(name)``

    Provides access to named Tag.

Miscellaneous
~~~~~~~~~~~~~

- ``timestamp(value)``

    This function is used to get a timestamp value, typically used to define time conditions on the
    query. For example you could use it to fetch only events that have not yet started:

    .. code-block:: yaml

        ...
            queries:
                pending_events:
                    query_type: SiteAPI:Location/Subtree
                    max_per_page: 10
                    page: 1
                    parameters:
                        content_type: event
                        field:
                            start_date:
                                gt: '@=timestamp("today")'

    .. note::

        Function ``timestamp()`` maps directly to the PHP's function `strtotime <https://secure.php.net/manual/en/function.strtotime.php>`_.
        That means it accepts any date and time format `supported by that function <https://secure.php.net/manual/en/datetime.formats.php>`_.

- ``split(value, delimiter = ",")``

    This function is used to split a string by a given delimiter, which defaults to the comma if
    omitted. For example, you could use it to split a string of comma-delimited ContentType
    identifiers:

    .. code-block:: yaml

        ...
            queries:
                pending_events:
                    query_type: SiteAPI:Location/Children
                    max_per_page: 10
                    page: 1
                    parameters:
                        content_type: '@=split(fieldValue("types").text)'

    Note that each returned string will also be trimmed of leading and trailing whitespace, and any
    empty values will be filtered out.

Templating
--------------------------------------------------------------------------------

Configured queries will be available in Twig templates, through ``ng_query`` or ``ng_raw_query``.
The difference it that the former will return a ``Pagerfanta`` instance, while the latter will
return an instance of ``SearchResult``. That also means ``ng_query`` will use ``max_per_page`` and
``page`` parameters to configure the pager, while ``ng_raw_query`` ignores them and executes the
configured query directly.

.. note::

    Queries are only executed as you access them through ``ng_query`` or ``ng_raw_query``. If you
    don't call those functions on any of the configured queries, none of them will be executed.

Both ``ng_query`` and ``ng_raw_query`` accept a single argument. This is the identifier of the
query, which is the key under the ``queries`` section, under which the query is configured.

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

.. note::

    You can't execute named queries. They are only available for referencing in concrete query
    configuration for a particular view.

.. hint::

    Execution of queries is **not cached**. If you call ``ng_query`` or ``ng_raw_query`` on the same
    query multiple times, the same query will be executed multiple times. If you need to access the
    query result multiple times, store it in a variable and access the variable instead.
