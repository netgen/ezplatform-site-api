Custom controllers
==================

Implementing a custom controller is similar to the vanilla eZ Platform. First, you have to implement
it with extending the Site API base controller:

.. code-block:: php

    namespace AppBundle\Controller;

    use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
    use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;

    class DemoController extends Controller
    {
        /**
         * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
         *
         * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
         */
        public function viewArticleAction(ContentView $view)
        {
            $content = $view->getSiteContent();
            $location = $view->getSiteLocation();

            $filterService = $this->getSite()->getFilterService();

            $hasRelatedItems = false;

            if ($content->hasField('related') && !$content->getField('related')->isEmpty()) {
                $hasRelatedItems = true;
            }

            // Your other custom logic here
            // ...

            // Add variables to the view
            $view->addParameters([
                'has_related_items' => $hasRelatedItems,
            ]);

            return $view;
        }
    }

Next, register your controller with the DI container. The Symfony's base controller expects that
``setContainer()`` is called on instantiation. You can do this manually:

.. code-block:: yaml

    app.controller.demo:
        class: AppBundle\Controller\DemoController
        calls:
            - [setContainer, ['@service_container']]

Or by extending the base definition:

.. code-block:: yaml

    app.controller.demo:
        parent: netgen.ezplatform_site.controller.base
        class: AppBundle\Controller\DemoController

Now you can use your custom controller in the view configuration:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        article:
                            template: "@App/content/full/article.html.twig"
                            controller: "app.controller.demo:viewArticleAction"
                            match:
                                Identifier\ContentType: article
