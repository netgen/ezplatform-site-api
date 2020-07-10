Installation
============

To install Site API first add it as a dependency to your project:

.. code-block:: console

    composer require netgen/ezplatform-site-api

Once Site API is installed, activate the bundle in ``app/AppKernel.php`` file by adding it to the
``$bundles`` array in ``registerBundles()`` method, together with other required bundles:

.. code-block:: php

    public function registerBundles()
    {
        //...

        $bundles[] = new Netgen\Bundle\EzPlatformSiteApiBundle\NetgenEzPlatformSiteApiBundle();
        $bundles[] = new Netgen\Bundle\EzPlatformSearchExtraBundle\NetgenEzPlatformSearchExtraBundle();

        return $bundles;
    }

And that's it. Once you finish the installation you will be able to use Site API services as you
would normally do in a Symfony application. However, at this point Site API is not yet fully
enabled. That is done per siteaccess, see :doc:`Configuration </reference/configuration>` page to
learn more.
