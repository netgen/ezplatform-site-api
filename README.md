# Netgen's Site API for eZ Platform

[![Build Status](https://img.shields.io/travis/netgen/ezplatform-site-api.svg?style=flat-square)](https://travis-ci.org/netgen/ezplatform-site-api)
[![Code Coverage](https://img.shields.io/codecov/c/github/netgen/ezplatform-site-api.svg?style=flat-square)](https://codecov.io/gh/netgen/ezplatform-site-api)
[![Quality Score](https://img.shields.io/scrutinizer/g/netgen/ezplatform-site-api.svg?style=flat-square)](https://scrutinizer-ci.com/g/netgen/ezplatform-site-api)
[![Downloads](https://img.shields.io/packagist/dt/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)
[![Latest stable](https://img.shields.io/packagist/v/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)
[![License](https://img.shields.io/packagist/l/netgen/ezplatform-site-api.svg?style=flat-square)](https://packagist.org/packages/netgen/ezplatform-site-api)

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

## Detailed usage instructions

The following document details what needs to be done to rewrite your existing site to Site API:

[Usage instructions](USAGE.md)

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

  echo $location->parent->contentInfo->name;
  ```

  ```php
  /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
  $loadService = $site->getLoadService();
  $content = $loadService->loadContent(24);
  $contentInfo = $loadService->loadContentInfo(12);

  foreach ($content->locations as $location) {
      // ...
  }

  foreach ($content->mainLocation->getChildren() as $child) {
      // ...
  }

  if (!$contentInfo->content->getField('image')->isEmpty()) {
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
  <h1>{{ content.name }} [{{ content.contentInfo.contentTypeIdentifier }}]</h1>

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

  <p>Parent name: {{ location.parent.contentInfo.name }}<p>

  <!-- 'children' variable is full Pagerfanta instance -->
  <p>Total blog posts: {{ children.nbResults }}</p>
  <p>Blog posts per page: {{ children.maxPerPage }}</p>
  <p>Page: {{ children.currentPage }}</p>

  <ul>
  {% for child in children %}
      <li>{{ child.contentInfo.name }}</li>
  {% endfor %}
  </ul>

  {{ pagerfanta( children, 'twitter_bootstrap' ) }}
  ```
