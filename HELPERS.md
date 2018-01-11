# Site API helpers

Site API comes with few helpers that help you to minimize amount of repetitive tasks.

### Traits

#### SearchResultExtractorTrait

Removes use of repetitive task of extracting value objects from `SearchResult` object. Abstract controller from Site API already uses given trait.

Example without trait:
```php
<?php

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

        $findService = $this-getSite()->getFindService();
	
	// prepare criteria
	// instantiate Query
	
	$results = $findService->findContent($query);
	
	$items = [];
	foreach($results->searchHits as $searchHit) {
		$items[] = $searchHit->valueObject;
	}

        $view->addParameters(
            array(
                'items' => $items,
            )
        );

        return $view;
    }
}
```

Example with usage of trait:
```php
<?php

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

        $findService = $this-getSite()->getFindService();
	
	// prepare criteria
	// instantiate Query
	
	$results = $findService->findContent($query);
	$items = $this->extractValueObjects($results);

        $view->addParameters(
            array(
                'items' => $items,
            )
        );

        return $view;
    }
}
```

`extractValueObjects` method works for both content and location search.
