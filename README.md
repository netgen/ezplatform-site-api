# Netgen's Site API for eZ Platform

[![Build Status](https://img.shields.io/travis/netgen/ezplatform-site-api.svg?style=flat-square)](https://travis-ci.org/netgen/ezplatform-site-api)
[![Code Coverage](https://img.shields.io/codecov/c/github/netgen/ezplatform-site-api.svg?style=flat-square)](https://codecov.io/gh/netgen/ezplatform-site-api)
[![Quality Score](https://img.shields.io/scrutinizer/g/netgen/ezplatform-site-api.svg?style=flat-square)](https://scrutinizer-ci.com/g/netgen/ezplatform-site-api)
[![Downloads](https://img.shields.io/packagist/dt/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)
[![Latest stable](https://img.shields.io/packagist/v/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)
[![License](https://img.shields.io/github/license/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)
[![PHP](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg?style=flat-square)](https://secure.php.net/)
[![eZ](https://img.shields.io/badge/eZ%20Platform-%3E%3D%201.0-EF5B2F.svg?style=flat-square)](https://ezplatform.com/)

## Features

- A set of read-only services on top of Repository API, made to transparently resolve correct translation

  - [`FilterService`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/FilterService.php)
  - [`FindService`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/FindService.php)
  - [`LoadService`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/LoadService.php)

- New set of aggregate objects, tailored to make building websites easier

  - [`Content`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/Values/Content.php)
  - [`ContentInfo`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/Values/ContentInfo.php)
  - [`Field`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/Values/Field.php)
  - [`Location`](https://github.com/netgen/ezplatform-site-api/blob/master/lib/API/Values/Location.php)

## Installation

To install Site API first add it as a dependency to your project:

```sh
composer require netgen/ezplatform-site-api:^2.4
```

Once Site API is installed, activate the bundle in `app/AppKernel.php` file by adding it to the `$bundles` array in `registerBundles()` method, together with other required bundles:

```php
public function registerBundles()
{
    ...

    $bundles[] = new Netgen\Bundle\EzPlatformSiteApiBundle\NetgenEzPlatformSiteApiBundle();
    $bundles[] = new Netgen\Bundle\EzPlatformSearchExtraBundle\NetgenEzPlatformSearchExtraBundle;

    return $bundles;
}
```

That will provide you with public Site API services defined in the [container](lib/Resources/config/services.yml),
which will enable you to rewrite your custom services piece by piece. At the same time controllers
and Twig templates can keep using the old code (meaning eZ Platform Repository API).

If you are starting from scratch, or once you're ready to fully switch to Site API, you can set it
as a default for URL alias routes with the following site-access aware config:

```yml
netgen_ez_platform_site_api:
    system:
        frontend_group:
            override_url_alias_view_action: true
```

For more details see [Usage instructions](USAGE.md).

## Detailed usage instructions

The following document details what needs to be done to rewrite your existing site to Site API:

[Usage instructions](USAGE.md)

[Getting started guide](GETTING_STARTED.md)

[Helpers](HELPERS.md)

[Changelog](CHANGELOG.md)

[Upgrade instructions](UPGRADE.md)

## Examples

- PHP
  ```php
  /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
  $loadService = $site->getLoadService();
  $location = $loadService->loadLocation(42);

  foreach ($location->getChildren(5) as $child) {
      // ...
  }

  foreach ($location->filterSiblings(['category']) as $sibling) {
      // ...
  }

  if (!$location->content->getField('image')->isEmpty()) {
      // ...
  }

  echo $location->parent->content->name;
  ```

  ```php
  /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
  $loadService = $site->getLoadService();
  $content = $loadService->loadContent(24);
  $location = $loadService->loadLocation(12);

  foreach ($content->locations as $location) {
      // ...
  }

  foreach ($content->mainLocation->getChildren() as $child) {
      // ...
  }

  if (!$location->content->getField('image')->isEmpty()) {
      // ...
  }
  ```

  ```php
  // Use eZ Publish Search API to find content from the configured
  // search engine (Solr or Legacy Search Engine).

  use eZ\Publish\API\Repository\Values\Content\LocationQuery;

  /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
  $findService = $site->getFindService();

  $searchResult = $findService->findLocations(new LocationQuery(...));
  ```

  ```php
  // Use eZ Publish Search API to find content from Legacy Search Engine.
  // That means you can use it in parallel to the Solr Search Engine
  // configured for the Repository. Advantage of having Legacy Search engine
  // available is in not needing indexing for data to become available in
  // search, because it works directly on the database. On the other hand,
  // it won't support faceting or full text search.

  use eZ\Publish\API\Repository\Values\Content\LocationQuery;

  /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
  $filterService = $site->getFilterService();

  $searchResult = $filterService->filterLocations(new LocationQuery(...));
  ```

- Twig

  ```twig
  <h1>{{ content.name }} [{{ content.contentTypeIdentifier }}]</h1>

  {% for identifier, field in content.fields %}
      <h4>Field '{{ identifier }}' in Content #{{ field.content.id }}</h4>
      {% if not field.isEmpty() %}
          {{ ng_render_field(field) }}
      {% else %}
          <p>Field of type '{{ field.fieldTypeIdentifier }}' is empty.</p>
      {% endif %}
  {% endfor %}

  <p>Title: {{ content.fields.title.value.text }}</p>
  <p>Same title repeated: {{ content.fields['title'].value.text }}</p>
  ```

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

