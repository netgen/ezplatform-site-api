# Site API helpers

Site API comes with few helpers that help you to minimize amount of repetitive tasks.

### Traits

#### [SearchResultExtractorTrait](lib/Core/Traits/SearchResultExtractorTrait.php)

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


#### [PagerfantaFindTrait](lib/Core/Traits/PagerfantaFindTrait.php)

Implements four helper method for creating  `\Pagerfanta\Pagerfanta` instance:

* `createContentSearchPager(Query $query, $currentPage, $maxPerPage)`
* `createContentSearchHitPager(Query $query, $currentPage, $maxPerPage)`
* `createLocationSearchPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)`
* `createLocationSearchHitPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)`

Abstract controller from Site API already uses given trait.


Example paging of Location search results without usage of trait:
```php
<?php

namespace AppBundle\Controller;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter;
use Pagerfanta\Pagerfanta;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;

class DemoController extends Controller
{
    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function viewArticleAction(ContentView $view)
    {
        // prepare criteria
        // ..
        $locationQuery = new LocationQuery();
	
        // set additional params on query
	
        $pager = new Pagerfanta(
            new LocationSearchAdapter($locationQuery, $this->getSite()->getFindService())
        );
        $pager->setMaxPerPage(9);
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setNormalizeOutOfRangePages(true);

        $view->addParameters(
            array(
                'pager' => $pager,
            )
        );

        return $view;
    }
}
```

Example paging of Location search results with usage of trait:
```php
<?php

namespace AppBundle\Controller;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;

class DemoController extends Controller
{
    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function viewArticleAction(ContentView $view)
    {
        // prepare criteria
        // ..
        $locationQuery = new LocationQuery();
	
        // set additional params on query
	
        $pager = $this->createLocationSearchPager(
            $locationQuery, $request->query->get('page', 1), 9
        );

        $view->addParameters(
            array(
                'pager' => $pager,
            )
        );

        return $view;
    }
}
```

Example paging of Content search results without usage of trait:
```php
<?php

namespace AppBundle\Controller;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchAdapter;
use Pagerfanta\Pagerfanta;
use eZ\Publish\API\Repository\Values\Content\Query;

class DemoController extends Controller
{
    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function viewArticleAction(ContentView $view)
    {
        // prepare criteria
        // ..
        $query = new Query();
	
        // set additional params on query
	
        $pager = new Pagerfanta(
            new ContentSearchAdapter($query, $this->getSite()->getFindService())
        );
        $pager->setMaxPerPage(9);
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setNormalizeOutOfRangePages(true);

        $view->addParameters(
            array(
                'pager' => $pager,
            )
        );

        return $view;
    }
}
```

Example paging of Content search results with usage of trait:
```php
<?php

namespace AppBundle\Controller;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use eZ\Publish\API\Repository\Values\Content\Query;

class DemoController extends Controller
{
    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function viewArticleAction(ContentView $view)
    {
       // prepare criteria
       // ..
       $query = new Query();
	
       // set additional params on query
	
       $pager = $this->createContentSearchPager(
           $query, $request->query->get('page', 1), 9
       );

       $view->addParameters(
            array(
                'pager' => $pager,
            )
        );

        return $view;
    }
}
```

#### [SiteAwareTrait](lib/Core/Traits/SiteAwareTrait.php)

Defines getter and setter method for classes that need to do operations on `Netgen\EzPlatformSiteApi\API\Site` service.
