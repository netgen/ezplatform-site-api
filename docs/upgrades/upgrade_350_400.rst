Upgrading from 3.5.0 to 4.0.0
=============================

Version 4.0.0 is a major release where all previous deprecations are removed.

Configuration changes
---------------------

Symfony semantic configuration located under ``netgen_ez_platform_site_api`` has been renamed and
moved under eZ Platform siteaccess aware configuration:

- ``override_url_alias_view_action`` has been renamed to ``ng_set_site_api_as_primary_content_view``
- ``named_objects`` has been renamed to ``ng_named_objects``
- ``use_always_available_fallback`` has been renamed to ``ng_use_always_available_fallback``
- ``fail_on_missing_fields`` has been renamed to ``ng_fail_on_missing_fields``
- ``render_missing_field_info`` has been renamed to ``ng_render_missing_field_info``

Previous configuration:

.. code-block:: yaml

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
                ng_set_site_api_as_primary_content_view: true
                ng_use_always_available_fallback: true
                ng_fail_on_missing_field: true
                ng_render_missing_field_info: true
                ng_named_objects: []

Some semantic configuration located under eZ Platform siteaccess aware configuration has been
renamed:

- ``ng_named_query`` has been renamed to ``ng_named_queries``
- ``ngcontent_view`` has been renamed to ``ng_content_views``

Previous configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_named_query: []
                ngcontent_view: []

New configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ng_named_queries: []
                ng_content_views: []

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
                ng_named_objects:
                    content_items:
                        certificate: 123
                    locations:
                        home: 2
                    tags:
                        colors: 456
