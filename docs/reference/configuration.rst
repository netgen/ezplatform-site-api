Configuration
=============

Site API has it's own view configuration, available under ``ngcontent_view`` key. Aside from
:doc:`Query Type </reference/query_types>` options documented separately, this is exactly the same
as eZ Platform's default view configuration under ``content_view`` key. You can use this
configuration right after the installation, but note that it won't be used for full views rendered
for eZ Platform URL aliases right away. Until you configure that, it will be used only when calling
its controller explicitly with ``ng_content:viewAction``.

To use Site API view rules for pages rendered from eZ Platform URL aliases, you have to enable it
for a specific siteaccess with the following semantic configuration:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                override_url_alias_view_action: true

Here ``frontend_group`` is the siteaccess group (or a siteaccess) for which you want to activate the
Site API. This switch is useful if you have some siteaccesses which can't use the it, like custom
admin or intranet interfaces.

.. note::

  To use Site API view configuration automatically on pages rendered from eZ Platform URL aliases,
  you need to enable it manually per siteaccess.

One you do this, all your **full view** templates and controllers will need to use Site API to keep
working. They will be resolved from Site API view configuration, available under ``ngcontent_view``
key. That means Content and Location variables inside Twig templates will be instances of Site API
Content and Location value objects, ``$view`` variable passed to your custom controllers will be an
instance of Site API ContentView variable, and so on.

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
                            template: 'Bundle:content/line:article.html.twig'
                            match:
                                Identifier\ContentType: article
                content_view:
                    line:
                        article:
                            template: 'Bundle:content/line:ez_article.html.twig'
                            match:
                                Identifier\ContentType: article

Rendering a line view for an article with ``ng_content:viewAction`` would use
``Bundle:content/line:article.html.twig`` template, while rendering a line view for an article with
``ez_content:viewAction`` would use ``Bundle:content/line:ez_article.html.twig`` template.

It is also possible to use custom controllers, this is documented on
:doc:`Custom controllers reference</reference/custom_controllers>` documentation page.

Handling Content Field inconsistencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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
render as an empty string or it will render useful debug information. By default it's value is
``false``, meaning it will render as an empty string. That behavior is also what you should use in
the production environment.

.. note::

  You can configure both ``render_missing_field_info`` and ``fail_on_missing_fields`` per siteaccess
  or siteaccess group.
