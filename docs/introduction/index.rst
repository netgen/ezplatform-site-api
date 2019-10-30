Introduction
============

The intention of this page is to give you a short overview of what Site API is. For that purpose we
can break the whole package into three main parts:

1. `Dedicated API layer`_
2. `Integration with eZ Platform`_
3. `Query Types`_

Dedicated API layer
-------------------

As Repository API was designed to be usable for general purpose, it can come as awkward and too
verbose when used for building websites. Site API fixes this by implementing a dedicated API layer
on top of eZ Platform Repository API which is designed for developing websites.

Having a dedicated layer enables us to take an extra step and do things you would not typically want
to do in Repository API. With Site API we can we can implement lazy loaded properties and methods
that enable content model traversal directly from the entities because:

1. it's a dedicated layer for building websites
2. | it's not intended to be layered (meaning no different API implementations
   | like Cache, Permission etc)

Handling multiple languages
~~~~~~~~~~~~~~~~~~~~~~~~~~~

The way Site API handles multiple languages was the initial motive for implementing it and deserves
to be mentioned separately.

Language configuration for a siteaccess consists of a prioritized list of languages. For example,
you could have a siteaccess with two languages, Croatian language as the most prioritized one and
English language as a fallback when Croatian translation does not exist:

.. code-block:: yaml

    ezpublish:
        system:
            cro:
                languages:
                    - 'cro-HR'
                    - 'eng-GB'

The intention here is that the siteaccess should first show content in Croatian language if it's
available, fallback to English translation when Croatian is not available and ignore any other
language. However, this is quite hard to implement correctly with vanilla Repository API, even with
the newest addition of siteaccess-aware Repository layer introduced in eZ Platform 7.2.

With Site API this comes out of the box and you don't have to pay special attention to it. All
possible ways to get a Content or a Location, whether through loading by ID, as a related Content,
accessing the field on the parent Location's Content, searching or using methods and properties on
the Site API objects -- it already respects this configuration. You can depend that you will always
get back only what can and should be rendered on the current siteaccess and then simply stop caring
about it, because it just works.

That feature alone significantly reduces cognitive load for developers, frees them from writing
tedious boilerplate code just to respect the language configuration, avoids ridiculous sanity checks
and mistakes and improves the overall developer experience.

Objects
~~~~~~~

Site API entities and values are similar to their counterparts in eZ Platform's Repository API:

- ``Content``

  The first difference from Repository Content is that it exist in a single translation,
  meaning it contains the fields for only one translation. That translation will always be the
  correct one to be rendered, resolved from the language configuration of the siteaccess. You won't
  need to choose the field in the correct translation, manually or through some kind of helper
  service. The Content's single translation is always the correct one.

  Content fields are lazy-loaded, which means they are loaded only if accessed. This voids the need
  to have a separate, light version of Content (ContentInfo in Repository API). Content object also
  provides properties and methods to enable access to Content's Locations and relations. Example
  usage from Twig:

  .. code-block:: twig

    <h1>{{ content.name }}</h1>
    <h2>Parent name: {{ content.mainLocation.parent.content.name }}</h2>
    <h3>Number of Locations: {{ content.locations|length }}</h3>

    {{ ng_render_field(content.fields.title) }}

    <ul>
        {% for relation in content.fieldRelations('articles') %}
            <li>{{ relation.title }}</li>
        {% endfor %}
    </ul>

- ``ContentInfo``

  The purpose of ContentInfo object in Repository API is to provide a lightweight version of Content
  object, containing only metadata (and omitting the fields). Since in Site API Content's fields are
  lazy-loaded, there is no real need for ContentInfo. Still, Site API provides it to keep the usage
  in templates similar to standard eZ Platform templates and through that make the migration and
  comparison easier.

  Site ContentInfo also provides access to data that is in Repository API available only through
  loading other objects, like ContentType identifier. Example usage from Twig:

  .. code-block:: twig

    <h2>Section ID: {{ content.contentInfo.sectionId }}</h2>
    <h2>ContentType identifier: {{ content.contentInfo.contentTypeIdentifier }}</h2>

  .. note::

    | In Site API it is not possible to load ``ContentInfo`` directly.
    | It is only available through properties on ``Content`` and ``Location`` objects.

- ``Location``

  Site ``Location`` is similar to Repository Location. It provides properties and methods to enable
  simple Location tree traversal (siblings, children, parents, ancestors etc). Example usage from
  Twig:

  .. code-block:: twig

    <h1>{{ location.content.name }} - Articles</h1>
    <h2>Parent: {{ location.parent.content.name }}</h2>
    <h3>Grandparent: {{ location.parent.parent.content.name }}</h3>

    {% set children = location.filterChildren(['article']) %}

    <ul>
    {% for child in children %}
        <li>{{ child.content.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( children, 'twitter_bootstrap' ) }}

- ``Field``

  ``Field`` object aggregates some properties from its FieldDefinition, like FieldType identifier,
  name and description. It also implements ``isEmpty()`` method, which makes simple to check if the
  field value is empty, without requiring external helpers. Example usage from Twig:

  .. code-block:: twig

    <h1>{{ content.fields.title.name }}</h1>
    <p>You can access the value directly: {{ content.fields.title.value.text }}</p>

    {% if not content.fields.title.empty %}
        <p>{{ ng_render_field( content.fields.title ) }}</p>
    {% endif %}

    {% set image = content.fields.image %}
    {% if not image.empty %}
        <img src="{{ ng_image_alias( image, 'i1140' ).uri }}"
             alt="{{ image.value.alternativeText }}" />
    {% endif %}

For your convenience all objects contain their corresponding Repository objects in properties
prefixed with ``inner``. Example usage from Twig:

.. code-block:: twig

  <h1>Content ID: {{ content.innerContent.id }}</h1>
  <h2>Location ID: {{ location.innerLocation.id }}</h2>
  <h3>Field ID: {{ field.innerField.id }}</h3>


For more details see :doc:`Templating </reference/objects>` and :doc:`Objects </reference/objects>` reference pages.

Services
~~~~~~~~

The API provides you with a set of **read-only** services:

1. ``LoadService``

  Provides methods to load Content and Locations by ID (and remote ID):

2. ``FindService``

  Provides methods to find Content and Locations using eZ Platform Repository Search API.

3. ``FilterService``

  This is quite similar to the ``FindService``, but only works with Legacy search engine, even if
  that is not the configured engine for the repository.

  Why? While Solr search engine provides more features and more performance than Legacy search
  engine, it's a separate system needs to be synchronized with changes in the database. This
  synchronization comes with a delay, which can be a problem in some cases.

  FilterService gives you access to search that is always up to date, because it uses Legacy search
  engine that works directly with database. At the same time, search on top of Solr, with all the
  advanced features (like fulltext search or facets) is still available through FindService.

4. ``RelationService``

  Provides methods for loading relations.

All services return only published Content and handle translations in a completely transparent way.
Language fallback configuration for the current siteaccess is automatically taken into account and
you will always get back only what should be rendered on the siteaccess. If the available
translation is not configured for a siteaccess, you won't be able to find or load Content or
Location. The services will behave as if it does not exist.

.. note::

  All of the Site API services are read-only. If you need to write to the eZ Platform's content
  repository, use its existing Repository API.

For more details see :doc:`Services reference </reference/services>` page.

Integration with eZ Platform
----------------------------

You can use the Site API services described above as you would normally do it a Symfony application.
But these are also integrated into eZ Platform's view layer. There is a Site API version of the view
configuration, available under ``ngcontent_view`` key:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    line:
                        article:
                            template: "@NetgenSite/content/line/article.html.twig"
                            match:
                                Identifier\ContentType: article

Aside from Query Type configuration described below, the format is exactly the same as eZ Platform's
view configuration under ``content_view`` key. Separate view configuration is also needed because we
need to handle it with code that will inject Site API objects to the template, instead of standard
eZ Platform objects. Together with this we provide Site API version of the Content View object,
which is used by the default Content view controller and :doc:`custom controllers </reference/custom_controllers>`.

With the configuration from above you you will be able to render a line view for an article by
executing a request to ``ng_content:viewAction``. However, that does not mean URL aliases will be
handled by the Site API view configuration as well. This needs to be explicitly enabled, per
siteaccess:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                override_url_alias_view_action: true

.. note::

    You can use the Site API's view configuration and eZ Platform's view configuration at the same
    time. However, URL aliases can be handled exclusively by the one or the other.

For more details see :doc:`Configuration reference </reference/configuration>` page.

Query Types
-----------

Query Types provide a set of predefined queries that can be configured for a specific view, as part
of the view configuration under ``ngcontent_view`` key. It also provides a system for developing new
queries inheriting common functionality.

While they can be used from PHP, main intention is to use them from the view configuration. This is
best explained with an example:

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

Other side of the configuration from the example above is full view ``folder`` template:

.. code-block:: twig

    {% set documents = ng_query( 'children_documents' ) %}

    <h3>Documents in this folder</h3>

    <ul>
    {% for document in documents %}
        <li>{{ document.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( documents, 'twitter_bootstrap' ) }}

If you used Legacy eZ Publish, this is similar to template fetch function. Important difference is
that in Legacy you used template fetch functions to pull the data into the template. Instead, with
Site API Query Types you push the data to the template. This keeps the logic out of the templates
and gives you better control and overview.

For more details see :doc:`Query Types reference </reference/query_types>` page.
