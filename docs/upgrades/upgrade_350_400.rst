Upgrading from 3.5.0 to 4.0.0
=============================

Version 4.0.0 is a major release where all previous deprecations are removed.

Configuration changes
---------------------

Semantic configuration located under eZ Platform siteaccess aware configuration has been renamed and
consolidated under `ng_site_api` key.

- ``ng_fallback_to_secondary_content_view`` has been renamed to ``fallback_to_secondary_content_view``
- ``ng_fallback_without_subrequest`` has been renamed to ``fallback_without_subrequest``
- ``ng_richtext_embed_without_subrequest`` has been renamed to ``richtext_embed_without_subrequest``
- ``ng_named_query`` has been renamed to ``named_queries``

Semantic configuration located under ``netgen_ez_platform_site_api`` has been renamed and moved
under ``ng_site_api`` key under eZ Platform siteaccess aware configuration:

- ``override_url_alias_view_action`` has been renamed to ``site_api_is_primary_content_view``
- ``fail_on_missing_fields`` has been renamed to ``fail_on_missing_field``

Previous configuration:

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

New configuration:

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

Previous configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view: []

New configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_content_view: []

Named object configuration has renamed keys used for named object types:

- ``content`` has been renamed to ``content_items``
- ``location`` has been renamed to ``locations``
- ``tag`` has been renamed to ``tags``

Previous configuration:

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

New configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_site_api:
                    named_objects:
                        content_items:
                            certificate: 123
                        locations:
                            home: 2
                        tags:
                            colors: 456
