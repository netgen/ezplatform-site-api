Configuration
=============

Site API has its own view configuration, available under ``ngcontent_view`` key. Aside from
:doc:`Query Type </reference/query_types>` configuration that is documented separately, this is
exactly the same as eZ Platform's default view configuration under ``content_view`` key. You can use
this configuration right after the installation, but note that it won't be used for full views
rendered for eZ Platform URL aliases right away. Until you configure that, it will be used only when
calling its controller explicitly with ``ng_content:viewAction``.

**Content on this page:**

.. contents::
    :depth: 1
    :local:

Configure handling of URL aliases
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To use Site API view rules for pages rendered from eZ Platform URL aliases, you have to enable it
for a specific siteaccess with the following semantic configuration:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                override_url_alias_view_action: true

Here ``frontend_group`` is the siteaccess group (or a siteaccess) for which you want to activate the
Site API. This switch is useful if you have a siteaccess that can't use the it, for example a custom
admin or intranet interface.

.. note::

  To use Site API view configuration automatically on pages rendered from eZ Platform URL aliases,
  you need to enable it manually per siteaccess.

Site API Content views
~~~~~~~~~~~~~~~~~~~~~~

One you enable ``override_url_alias_view_action`` for a siteaccess, all your **full view** templates
and controllers will need to use Site API to keep working. They will be resolved from Site API view
configuration, available under ``ngcontent_view`` key. That means Content and Location variables
inside Twig templates will be instances of Site API Content and Location value objects, ``$view``
variable passed to your custom controllers will be an instance of Site API ContentView variable, and
so on.

If needed you can still use ``content_view`` rules. This will allow you to have both Site API
template override rules as well as original eZ Platform template override rules, so you can rewrite
your templates bit by bit. You can decide which one to use by calling either
``ng_content:viewAction`` or ``ez_content:viewAction`` controller.

.. tip::

    | View configuration is the only eZ Platform configuration regularly edited
    | by frontend developers.

For example, if using the following configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    line:
                        article:
                            template: '@App/content/line/article.html.twig'
                            match:
                                Identifier\ContentType: article
                content_view:
                    line:
                        article:
                            template: '@App/content/line/ez_article.html.twig'
                            match:
                                Identifier\ContentType: article

Rendering a line view for an article with ``ng_content:viewAction`` would use
``@App/content/line/article.html.twig`` template, while rendering a line view for an article with
``ez_content:viewAction`` would use ``@App/content/line/ez_article.html.twig`` template.

It is also possible to use custom controllers, this is documented on
:doc:`Custom controllers reference</reference/custom_controllers>` documentation page.

.. _named_object_configuration:

Named objects
~~~~~~~~~~~~~

Named objects feature provides a way to configure specific objects (``Content``, ``Location`` and
``Tag``) by name and ID, and a way to access them by name from PHP, Twig and Query Type
configuration.

Example configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                named_object:
                    content:
                        certificate: 42
                        site_info: 'abc123'
                    location:
                        homepage: 2
                        articles: 'zxc456'
                    tag:
                        categories: 24
                        colors: 'bnm789'

From the example, ``certificate`` and ``site_info`` are names of Content objects, ``homepage`` and
``articles`` are names of Location objects and ``categories`` and ``colors`` are names of Tag
objects. The example also shows it's possible to use both a normal ID (integer) or remote ID
(string). In fact, it shows a short syntax, where the type of ID is inferred from the type, while
full syntax equivalent to the above would be:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                named_object:
                    content:
                        certificate:
                            id: 42
                        site_info:
                            remote_id: 'abc123'
                    location:
                        homepage:
                            id: 2
                        articles:
                            remote_id: 'zxc456'
                    tag:
                        categories:
                            id: 24
                        colors:
                            remote_id: 'bnm789'

Accessing named objects
-----------------------

- access from PHP is :ref:`documented on the Services page<named_object_php>`
- access from Twig is :ref:`documented on Templating page<named_object_template>`
- access from Query Type configuration is :ref:`documented on Query Types page<named_object_query_types>`

Content Field inconsistencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Sometimes when the content model is changed or for any reason the data is not consistent, it can
happen that some Content Fields are missing. In case of content model change that is a temporary
situation lasting while the data is being updated in the background. But even in the case of
inconsistent database, typically you do not want that to result in site crash.

To account for this Site API provides the following semantic configuration:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                fail_on_missing_fields: true
                render_missing_field_info: false

By default ``fail_on_missing_fields`` is set to ``%kernel.debug%`` container parameter, which means
accessing a nonexistent field in ``dev`` environment will fail and result in a ``RuntimeException``.

On the other hand, when not in debug mode (in ``prod`` environment), the system will not crash, but
will instead return a special ``Surrogate`` type field, which always evaluates as empty and renders
to an empty string. In this case, a ``critical`` level message will be logged, so you can find and
fix the problem.

Second configuration option ``render_missing_field_info`` controls whether ``Surrogate`` field will
render as an empty string or it will render useful debug information. By default its value is
``false``, meaning it will render as an empty string. That behavior is also what you should use in
the production environment. Setting this option to ``true`` can be useful in debug mode, together
with setting ``fail_on_missing_fields`` to ``false``, as that will provide a visual cue about the
missing field without the page crashing and without the need to go into the web debug toolbar to
find the logged message.

.. note::

  You can configure both ``render_missing_field_info`` and ``fail_on_missing_fields`` per siteaccess
  or siteaccess group.
