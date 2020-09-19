Upgrading from 3.5.0 to 4.0.0
=============================

Version 4.0.0 is a major release where all previous deprecations have been removed and several
breaking changes have been introduced.

Removed ``RenderContentEvent``
------------------------------

Event ``RenderContentEvent`` and the associated class ``SiteApiEvents`` have been removed. If you
used this event, upgrade by using new ``RenderViewEvent`` instead.

Removed loading Content relations by ID
---------------------------------------

Using Content ID with ``RelationService`` methods ``loadFieldRelation()`` and
``loadFieldRelations()`` has been removed. Now, only Content instance is allowed. If you used these
methods directly, you can upgrade your code by providing Content instance instead of the ID.

Enabled view fallback without a subrequest
------------------------------------------

View fallback without a subrequest has been enabled by default. If you depended on view fallback
being disabled or not avoiding a subrequest, upgrade by explicitly configuring the options as
needed:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_site_api:
                    fallback_to_secondary_content_view: false
                    fallback_without_subrequest: false

Enabled filtering out non-visible items in Query Types
------------------------------------------------------

Previously, you had to explicitly set the condition on Location visibility when configuring Query
Types. By default, both visible and non visible Locations were returned. Now, both Content and
Location Query Types will return only visible items by default. This can be overridden on the Query
Type level by explicitly configuring visibility.

You can also configure the default behaviour by the siteaccess:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_site_api:
                    show_hidden_items: true

Restructured configuration
--------------------------

Semantic configuration located under eZ Platform siteaccess aware configuration has been renamed and
consolidated under ``ng_site_api`` key.

- ``ng_fallback_to_secondary_content_view`` has been renamed to ``fallback_to_secondary_content_view``
- ``ng_fallback_without_subrequest`` has been renamed to ``fallback_without_subrequest``
- ``ng_richtext_embed_without_subrequest`` has been renamed to ``richtext_embed_without_subrequest``
- ``ng_named_query`` has been renamed to ``named_queries``

Semantic configuration located under ``netgen_ez_platform_site_api`` has been renamed and moved
under ``ng_site_api`` key under eZ Platform siteaccess aware configuration:

- ``override_url_alias_view_action`` has been renamed to ``site_api_is_primary_content_view``
- ``fail_on_missing_fields`` has been renamed to ``fail_on_missing_field``

Old configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_fallback_to_secondary_content_view: true
                ng_fallback_without_subrequest: true
                ng_richtext_embed_without_subrequest: true
                ng_named_query: []
    netgen_ez_platform_site_api:
        system:
            frontend_group:
                override_url_alias_view_action: true
                use_always_available_fallback: true
                fail_on_missing_fields: true
                render_missing_field_info: true
                named_objects: []

Upgraded configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_site_api:
                    site_api_is_primary_content_view: true
                    fallback_to_secondary_content_view: true
                    fallback_without_subrequest: true
                    richtext_embed_without_subrequest: true
                    use_always_available_fallback: true
                    fail_on_missing_field: true
                    render_missing_field_info: true
                    named_objects: []
                    named_queries: []

Key for Site API content view configuration has been renamed from ``ngcontent_view`` to
``ng_content_view``:

Old configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view: []

Upgraded configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_content_view: []

Named object configuration has renamed keys used for named object types:

- ``location`` has been renamed to ``locations``
- ``tag`` has been renamed to ``tags``

Old configuration:

.. code-block:: yaml

    netgen_ez_platform_site_api:
        system:
            frontend_group:
                named_objects:
                    content:
                        certificate: 123
                    location:
                        home: 2
                    tag:
                        colors: 456

Upgraded configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_site_api:
                    named_objects:
                        content:
                            certificate: 123
                        locations:
                            home: 2
                        tags:
                            colors: 456
