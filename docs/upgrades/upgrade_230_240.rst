Upgrading from 2.3.0 to 2.4.0
=============================

Controllers that extend from ``Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller`` and are
registered inside dependency injection container should set two setter injection ``calls``:

.. code-block:: yaml

    app.demo.controller.demo_controller:
        class: Acme\Bundle\DemoBundle\Controller\DemoController
        calls:
            - [setContainer, ['@service_container']]
            - [setSite, ['@netgen.ezplatform_site.site']]


Or if you want to avoid setter calls, just set ``parent`` service:

.. code-block:: yaml

    app.demo.controller.demo_controller:
        parent: netgen.ezplatform_site.controller.base
        class: Acme\Bundle\DemoBundle\Controller\DemoController
