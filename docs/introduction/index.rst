Introduction
============

Site API is a lightweight layer built on top of eZ Platform's Repository API. It's purpose is to
provide better developer experience in context of building websites. It will increase developer's
productivity, but also -- it will open most the process to roles other than just PHP developers.

For the purpose of this introduction, we can break the whole package into three main parts:

1. `Dedicated API layer`_
2. `Integration of the API with eZ Platform`_
3. `Query Types`_

Dedicated API layer
-------------------

As Repository API was designed to be usable for general purpose, it can come as awkward or too
verbose when used for building websites. Site API implements a dedicated API layer on top of
eZ Platform Repository API which is designed for developing websites.

Services
~~~~~~~~

The API provides you with a set of read-only services:

1. ``LoadService``

  Provides methods to load Content and Locations by ID (and remote ID):

2. ``FindService``

  Provides methods to find Content and Locations using eZ Platform Repository Search API.

3. ``FilterService``

  This is quite similar to the ``FindService``, but only works with Legacy search engine, even if
  that is not the configured egine for the repository.

  Why? While Solr search engine provides more features and more performance than Legacy search
  engine, it's a separate system needs to be synchronized with the changes in the database. This
  synchronization comes with a delay, which can be a problem in some cases.

  FilterService gives you access to search that is always up to date, because it uses Legacy search
  engine that works directly with database. At the same time, search on top of Solr, with all the
  advanced features (like fulltext search or facets) is still available through FindService.

4. ``RelationService``

  Provides methods for loading relations.

All of these services make handling of languages completely transparent. Language fallback
configuration for the current siteaccess is automatically taken into account and you will always get
back only what should be rendered on the siteaccess. If the translation is not configured for a
siteaccess, you won't be able to find or load it -- the system will behave as if it does not exist.

.. note::

  All of the Site API services are read-only. If you need to write to the eZ Platform's content
  repository, use it's existing Repository API. (link)

Objects
~~~~~~~

The services return their own objects, similar but different from their counterparts in
Repository API. Having a layer that is dedicated for building websites enables us to take an extra
step and do things you would not typically want to do in Repository API:

- ``Content``

  The first difference from Repository Content is that it exist it a single translation only,
  meaning it contains the fields for only one translation. That will always be the translation to be
  rendered on the siteaccess. You won't need to choose the field in the correct translation,
  manually or through some kind of helper service. There is only one translation - the correct one.

  Fields are actually lazy-loaded, which means they are loaded only if accessed. This voids the
  need to have separate, light version of Content (ContentInfo in Repository API).
  It also provides you with some additional properties and methods. Example usage from Twig:

  .. code-block:: twig

    <h1>{{ content.name }}</h1>
    <h2>Parent name: {{ content.mainLocation.parent.content.name }}</h2>
    <h3>Number of Locations: {{ content.locations|length }}</h3>

    {% for field in content.fields %}
        {% if not field.empty %}
            {{ ng_render_field(field) }}
        {% endif %}
    {% endfor %}

- ``ContentInfo``

  Above we said that ``Content``'s fields are lazy-loaded, which voids the need for ``ContentInfo``.
  Still, Site API has it's own version of ``ContentInfo``. The reason for this is to keep the
  usage in templates similar to standard eZ Platform templates and through that make the migration
  easier. Example usage from Twig:

  .. code-block:: twig

    <h2>Section ID: {{ content.contentInfo.sectionId }}</h2>
    <h2>ContentType identifier: {{ content.contentInfo.contentTypeIdentifier }}</h2>

  .. note::

    In Site API is not possible to load ``ContentInfo`` directly.
    It is only available from ``Content`` and ``Location`` objects.

- ``Location``

  Site ``Location`` is very similar to Repository Location, but the objects it aggregates objects
  come from Site API and not from Repository. It also provides methods for simple tree traversal.
  Example usage from Twig:

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

  ``Field`` object aggregates some properties from it's FieldDefinition, like FieldType identifier,
  or name and description. It also provides ``isEmpty()`` method, which makes simple to check if the
  field value is empty, without external helpers. Example usage from Twig:

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

For your convenience, all objects contain their corresponding Repository objects in properties
prefixed with ``inner``. Example usage from Twig:

.. code-block:: twig

  <h1>Content ID: {{ content.innerContent.id }}</h1>
  <h2>Location ID: {{ location.innerLocation.id }}</h2>
  <h3>Field ID: {{ field.innerField.id }}</h3>

Integration of the API with eZ Platform
---------------------------------------

You can use the Site API services described above as you would normally do it a Symfony application.
But these are also integrated into eZ Platform's view layer. That means you have Site API version of
the view configuration, available under ``ngcontent_view`` key:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    line:
                        article:
                            template: "NetgenSiteBundle:content/line:article.html.twig"
                            match:
                                Identifier\ContentType: article

With that, you can render a live view for an article by executing a request to
``ng_content:viewAction``. However, that does not mean URL aliases will be handed by the Site API
view configuration. This needs to be explicitly enabled, per siteaccess:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                override_url_alias_view_action: true

.. note::

    You can use the Site API's view configuration and eZ Platform's view configuration at the same
    time. However, URL aliases can be handled exclusively by the one or the other.

Query Types
-----------

Query Types feature provides a set of predefined queries that can be configured for a specific view,
as part of the view configuration under ``ngcontent_view`` key. It also provides a system for
developing new queries inheriting common functionality.

While they can be used from PHP, main intention is to use them from the view configuration. How that
works is best explained with an example:

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
                                    query_type: SiteAPI:Content/Location/Children
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        content_type: document
                                        section: restricted
                                        sort: priority desc

Other side of the configuration from above is full view ``folder`` template:

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

For more details see :doc:`Query Types documentation page </reference/query_types>`.
