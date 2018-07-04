Installation instructions
=========================

To install Site API first add it as a dependency to your project:

.. code-block:: shell

    $ composer require netgen/ezplatform-site-api:^2.5

Once Site API is installed, activate the bundle in ``app/AppKernel.php`` file by adding it to the
``$bundles`` array in ``registerBundles()`` method, together with other required bundles:

.. code-block:: php

    public function registerBundles()
    {
        ...

        $bundles[] = new Netgen\Bundle\EzPlatformSiteApiBundle\NetgenEzPlatformSiteApiBundle();
        $bundles[] = new Netgen\Bundle\EzPlatformSearchExtraBundle\NetgenEzPlatformSearchExtraBundle;

        return $bundles;
    }
