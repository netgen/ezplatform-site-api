Some common examples showcasing Site API usage.

# 1. Load Content

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$content = $contentService->loadContent(42, $languages);
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);
```

## Remarks

- Repository API requires a list of languages
- Repository API will return Content in all given languages from the given list (if they exist), but only one will be rendered
- Site API returns the Content only in the language that will be rendered on the siteaccess

# 2. Load Location

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();
$locationService = $repository->getLocationService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$location = $locationService->loadLocation(42);

// Can we show this Location on the siteaccess?

$versionInfo = $contentService->loadVersionInfoById($location->contentId);

$languageCodesSet = array_flip($versionInfo->languageCodes);
$canShowLocation = false;

foreach ($languages as $languageCode) {
    if (isset($languageCodesSet[$languageCode])) {
        $canShowLocation = true;
        break;
    }
}

if (!$canShowLocation) {
    throw new NotFoundException('404 not found');
}
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$location = $loadService->loadLocation(42);
```

## Remarks

- Repository API does't take languages into account, it takes a lot of work to take care of it manually
- Site API takes care of that automatically, it won't find the Location if the language should not be rendered on the siteaccess

# 3. Load Location's Content

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$locationService = $repository->getLocationService();
$contentService = $repository->getContentService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$location = $locationService->loadLocation(42);
$content = $contentService->loadContent($location->contentId, $languages);
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$location = $loadService->loadLocation(42);
$content = $location->content;
```

## Remarks

- no additional call to get the Content
- Content is lazy loaded only when accessed!

# 4. Load ContentInfo and access Content fields only when needed

For example you might want to do this for performance, loading ContentInfo is faster because doesn't contain fields.

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

// We can't load only ContentInfo, because if does not contain language information

$versionInfo = $contentService->loadVersionInfoById(42);

$languageCodesSet = array_flip($versionInfo->languageCodes);
$canShowContent = false;

foreach ($languages as $languageCode) {
    if (isset($languageCodesSet[$languageCode])) {
        $canShowContent = true;
        break;
    }
}

if ($canShowContent && $versionInfo->contentInfo->contentTypeId === 239458721341) {
    $content = $contentService->loadContent(42, $languages);

    $field = $content->getField('name');
    // ...
}
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);

if ($content->contentInfo->contentTypeIdentifier === 'article') {
    $field = $content->getField('name');
    // ...
}
```

## Remarks

- Content fields are lazy loaded only when accessed!
- With Repository API doing something as simple as this can be very involving
- With Site API it just comes natural

# 5. Find the name of parent Content

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();
$locationService = $repository->getLocationService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$content = $contentService->loadContent(42, $languages);
$mainLocation = $locationService->loadLocation($content->contentInfo->mainLocationId);
$parentLocation = $locationService->loadLocation($mainLocation->parentLocationId);
$parentContent = $contentService->loadContent($parentLocation->contentId, $languages);

$name = $parentContent->getVersionInfo()->getName();
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);

$name = $content->mainLocation->parent->content->name;
```

## Remarks

- properties are lazy loaded only when accessed!
- with Site API simple content traversal is simple

# 6. Check if the field is empty

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$content = $contentService->loadContent(42, $languages);

/** @var \eZ\Publish\Core\Helper\FieldHelper */
if ($fieldHelper->isFieldEmpty($content, 'image')) {
    // ...
}
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);

if (!$content->getField('image')->isEmpty()) {
    // ...
}
```

## Remarks

- no special helper needed to check if the field is empty

# 7. Find images under Content's main Location

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();
$locationService = $repository->getLocationService();
$searchService = $repository->getSearchService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$content = $contentService->loadContent(42, $languages);
$mainLocation = $locationService->loadLocation($content->contentInfo->mainLocationId);

$query = new Query([
    'filter' => new LogcialAnd([
        new ParentLocationId($mainLocation->id),
        new ContentTypeIdentifier('image'),
    ]),
]);

$searchResult = $searchService->findContent($query, ['languages' => $languages]);
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);

$images = $content->mainLocation->filterChildren(['images']);
```

## Remarks

- again - properties are lazy loaded only when accessed
- `$images` is an instance of `Pagerfanta` - you can iterate over it directly or use it to build a pager

# 8. Find all siblings of the same ContentType

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();
$locationService = $repository->getLocationService();
$searchService = $repository->getSearchService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$location = $locationService->loadLocation(42);

$query = new LocationQuery([
    'filter' => new LogcialAnd([
        new ParentLocationId($location->parentLocationId),
        new ContentTypeId($location->contentInfo->contentTypeId),
        new LogicalNot(
            new LocationId($location->id)
        ),
    ]),
]);

$searchResult = $searchService->findLocations($query, ['languages' => $languages]);
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$location = $loadService->loadContent(42);

$siblings = $location->filterSiblings([$location->contentInfo->contentTypeIdentifier]);
```

## Remarks

- kind of a syntax sugar
- this would be similar to `$location->parent->filterChildren([$location->contentInfo->contentTypeIdentifier])`, but it excludes current Location automatically

# 9. Find all Content's field relations of the 'image' type

## Before

```php
/** @var $repository \eZ\Publish\API\Repository\Repository */
$contentService = $repository->getContentService();
$searchService = $repository->getSearchService();

/** @var $configResolver \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver */
$languages = $configResolver->getParameter('languages');

$content = $locationService->loadContent(42, $languages);
$relationField = $content->getField('related_content');
$relatedContentIds = $relationField->value->destinationContentIds;

// We have to use search to account for permissions and status

$query = new Query([
    'filter' => new LogcialAnd([
        new ContentId($relatedContentIds),
        new ContentTypeId(1234234512),
    ]),
]);

$searchResult = $searchService->findLocations($query, ['languages' => $languages]);

// Now we have the result, but the relation order is not ensured by search

$relatedContentItems = [];

foreach ($searchResult->searchHit as $searchHit) {
    $relatedContentItems[] = $searchHit->valueObject;
}

$sortedIdList = array_flip($relatedContentIds);

$sorter = function (Content $content1, Content $content2) use ($sortedIdList) {
    if ($content1->id === $content2->id) {
        return 0;
    }

    return ($sortedIdList[$content1->id] < $sortedIdList[$content2->id]) ? -1 : 1;
};

usort($relatedContentItems, $sorter);
```

## Now

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$loadService = $site->getLoadService();

$content = $loadService->loadContent(42);

$relatedContentItems = $content->filterFieldRelations('related_content', ['image']);
```

# 10. Full Repository search API is still available

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$findService = $site->getFindService();

// This is unchanged Repository Search API
$query = new Query();

// Search result contains Site API Content items
$searchResult = $findService->findContent($query);

// This is unchanged Repository Search API
$locationQuery = new LocationQuery();

// Search result contains Site API Locations
$searchResult = $findService->findLocations($locationQuery);
```

# 11. Search functionality that does not depend on Solr

Sometimes you don't want to depend on the asynchronous nature of indexing data in Solr. FilterService
provides Search API on top of Legacy Search Engine, in parallel to using Solr as the Repository's search engine.
There is no similar functionality in Repository API. You could access Solr search handler to force commit,
and then wait for a certain amount of time to ensure data is available for search, but that's poor solution.

For various reasons Repository API will need to implement something like this in the future, it was already proposed in:

https://github.com/ezsystems/ezpublish-kernel/pull/1636

```php
/** @var $site \Netgen\EzPlatformSiteApi\API\Site */
$filterService = $site->getFilterService();

$query = new Query();

$searchResult = $findService->filterContent($query);
```

- it mostly behaves the same as FindService, only uses different search engine (Legacy)
- fulltext search (Fulltext criterion) should not be used with FilterService, as the fulltext data
will be indexed only for the configured search engine (assuming Solr)

# 12. Some comparison of usage from Twig

Access Content fields directly through dot or array notation:

#### Before

```twig
{ ez_render_field( content, 'title' ) }
```

#### Now

```twig
{ ng_render_field( content.fields.title ) }
```

```twig
{ ng_render_field( content.fields['title'] ) }
```

Check if the field is empty without helper function:

#### Before

```twig
{% if ez_is_field_empty( content, 'image' ) %}
    ...
{% endif %}
```

#### Now

```twig
{% if content.fields.image.empty %}
    ...
{% endif %}
```

Simplified image alias rendering:

#### Before

```twig
{ ez_image_alias( content.field( 'image' ), content.versionInfo, 'large' ) }
```

#### Now

```twig
{ ng_image_alias( content.fields.image, 'large' ) }
```

# 13. An example of usage from Twig

```twig
{% set children = location.filterChildren(['blog_post'], 10, 2) %}

<p>Parent name: {{ location.parent.content.name }}<p>

<!-- 'children' variable is full Pagerfanta instance -->
<p>Total blog posts: {{ children.nbResults }}</p>
<p>Blog posts per page: {{ children.maxPerPage }}</p>
<p>Page: {{ children.currentPage }}</p>

<ul>
{% for child in children %}
    <li>{{ child.content.name }}</li>
{% endfor %}
</ul>

{{ pagerfanta( children, 'twitter_bootstrap' ) }}
```
