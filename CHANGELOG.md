eZ Platform Site API changelog
==============================

3.5.0 (Unreleased)
------------------

[`3.4.3...3.5.0`](https://github.com/netgen/ezplatform-site-api/compare/3.4.3...3.5.0)

### Added
* NGSTACK-411: using an unsupported Location child sort will now log a notice instead of causing a
  fatal crash by @RandyCupic ([#170](https://github.com/netgen/ezplatform-site-api/pull/170))

### Fixed
* Fixed documentation code example by @leohajder ([#166](https://github.com/netgen/ezplatform-site-api/pull/166))
* NGSTACK-416: Rendering a full view through automatic view fallback will now correctly set `layout`
  variable ([#172](https://github.com/netgen/ezplatform-site-api/pull/172))
* NGSTACK-429: Previewing a Content containing field relations will now correctly render draft
  relations instead of the published ones ([#173](https://github.com/netgen/ezplatform-site-api/pull/173))
* NGSTACK-423: Previewing a Content rendered through automatic view fallback will now correctly
  render the draft version instead of the published one ([#174](https://github.com/netgen/ezplatform-site-api/pull/174))

### Deprecated
* Content view renderer event implementation has been moved to bundle ([#146](https://github.com/netgen/ezplatform-site-api/pull/146))

  Event `Netgen\EzPlatformSiteApi\Event\SiteApiEvents::SiteApiEvents`
  is deprecated and replaced by `Netgen\Bundle\EzPlatformSiteApiBundle\Events::RENDER_VIEW`, event
  object ` namespace Netgen\EzPlatformSiteApi\Event\RenderContentEvent` is deprecated and replaced by `Netgen\Bundle\EzPlatformSiteApiBundle\Event\RenderViewEvent`. Deprecated
  implementation will be removed in `4.0`.

3.4.3 (03.07.2020)
------------------

[`3.4.2...3.4.3`](https://github.com/netgen/ezplatform-site-api/compare/3.4.2...3.4.3)

### Fixed

* Fix code example in documentation by @leohajder ([#162](https://github.com/netgen/ezplatform-site-api/pull/162))
* Handle UnauthorizedException when accessing named objects from Twig by @leohajder ([#164](https://github.com/netgen/ezplatform-site-api/pull/164))

3.4.2 (15.05.2020)
------------------

[`3.4.1...3.4.2`](https://github.com/netgen/ezplatform-site-api/compare/3.4.1...3.4.2)

### Added
* Copy button on documentation code and configuration examples

### Fixed
* Documentation fixes by @zulicek
* Documentation fixes by @leohajder ([#160](https://github.com/netgen/ezplatform-site-api/pull/160))
* NGSTACK-413: Fix checking if Relation value is empty by @RandyCupic ([#161](https://github.com/netgen/ezplatform-site-api/pull/161))

3.4.1 (31.03.2020)
------------------

[`3.4.0...3.4.1`](https://github.com/netgen/ezplatform-site-api/compare/3.4.0...3.4.1)

### Fixed
* Documentation fixes by @leohajder ([#158](https://github.com/netgen/ezplatform-site-api/pull/158))
* NGSTACK-410: fix extended config not overriding default values ([#159](https://github.com/netgen/ezplatform-site-api/pull/159))

3.4.0 (03.02.2020)
------------------

[`3.3.1...3.4.0`](https://github.com/netgen/ezplatform-site-api/compare/3.3.1...3.4.0)

### Added
* NGSTACK-377: variables in template identifier configuration (#156)
* NGSTACK-389: implement view configuration inheritance (#157)

3.3.1 (27.01.2020)
------------------

[`3.3.0...3.3.1`](https://github.com/netgen/ezplatform-site-api/compare/3.3.0...3.3.1)

### Fixed
* Use view type from the `app.request` (https://github.com/netgen/ezplatform-site-api/commit/86f3f73141d79a177eeade701ff7733fa6aa4dd2)

3.3.0 (15.01.2020)
------------------

[`3.2.2...3.3.0`](https://github.com/netgen/ezplatform-site-api/compare/3.2.2...3.3.0)

### Added
* Add `Location::getFirstChild()` method ([#148](https://github.com/netgen/ezplatform-site-api/pull/148))
* Add `Content::getFirstNonEmptyField()` method ([#150](https://github.com/netgen/ezplatform-site-api/pull/150))
* NGSTACK-371: implement fallback between eZ and Site API content views ([#152](https://github.com/netgen/ezplatform-site-api/pull/152))
* Implement Content View Location resolver ([#153](https://github.com/netgen/ezplatform-site-api/pull/153))
* eZ Platform v3 support: missing argument from EZP-30882 by @wizhippo ([#154](https://github.com/netgen/ezplatform-site-api/pull/154))
* NGSTACK-354: implement support for XmlText and RichText embed rendering ([#155](https://github.com/netgen/ezplatform-site-api/pull/155))

### Fixed
* Fix preview of the non-published Content ([#149](https://github.com/netgen/ezplatform-site-api/pull/149))
* Fix typo in configuration by @joezg ([#151](https://github.com/netgen/ezplatform-site-api/pull/151))
* Fix service ID for named object provider (https://github.com/netgen/ezplatform-site-api/commit/4ee25ebc26b801aececd55b198e7c1dad7ef2bce)

3.2.2 (28.11.2019)
------------------

[`3.2.1...3.2.2`](https://github.com/netgen/ezplatform-site-api/compare/3.2.1...3.2.2)

### Fixed
* Fix preview of the unpublished Content ([#149](https://github.com/netgen/ezplatform-site-api/pull/149))

3.2.1 (18.11.2019)
------------------

[`3.2.0...3.2.1`](https://github.com/netgen/ezplatform-site-api/compare/3.2.0...3.2.1)

### Fixed
* Make `TagsService` argument optional

3.2.0 (12.11.2019)
------------------

[`3.1.0...3.2.0`](https://github.com/netgen/ezplatform-site-api/compare/3.1.0...3.2.0)

### Added
* NGSTACK-347: enable redirects through content view configuration by @iherak ([#147](https://github.com/netgen/ezplatform-site-api/pull/147))

3.1.0 (04.11.2019)
------------------

[`3.0.5...3.1.0`](https://github.com/netgen/ezplatform-site-api/compare/3.0.5...3.1.0)

### Added
* NGSTACK-250: `modification_date` query type condition ([#140](https://github.com/netgen/ezplatform-site-api/pull/140))
* NGSTACK-336: Named Objects ([#145](https://github.com/netgen/ezplatform-site-api/pull/145))

3.0.5 (04.11.2019)
------------------

[`3.0.4...3.0.5`](https://github.com/netgen/ezplatform-site-api/compare/3.0.4...3.0.5)

### Fixed
* Remove configuration for the default full view ([#144](https://github.com/netgen/ezplatform-site-api/pull/144))

3.0.4 (23.10.2019)
------------------

[`3.0.3...3.0.4`](https://github.com/netgen/ezplatform-site-api/compare/3.0.3...3.0.4)

### Fixed
* Content render events by @emodric ([#141](https://github.com/netgen/ezplatform-site-api/pull/141))
* Reset ezsettings parameters only if they do not exist by @emodric ([#142](https://github.com/netgen/ezplatform-site-api/pull/142))

3.0.3 (08.10.2019)
------------------

[`3.0.2...3.0.3`](https://github.com/netgen/ezplatform-site-api/compare/3.0.2...3.0.3)

### Fixed
* Use Twig notation for template paths by @emodric ([#134](https://github.com/netgen/ezplatform-site-api/pull/134))
* Fix deprecations on Symfony 3.4 and 4.0 by @emodric ([#136](https://github.com/netgen/ezplatform-site-api/pull/136))
* Kernel v3 compatibility ([#137](https://github.com/netgen/ezplatform-site-api/pull/137))
* Travis build is migrated to travis-ci.com by @emodric ([#138](https://github.com/netgen/ezplatform-site-api/pull/138))

3.0.2 (25.09.2019)
------------------

[`3.0.1...3.0.2`](https://github.com/netgen/ezplatform-site-api/compare/3.0.1...3.0.2)

### Added
* Allow eZ value objects when rendering content without subrequests by @emodric (#133)

3.0.1 (04.09.2019)
------------------

[`3.0.0...3.0.1`](https://github.com/netgen/ezplatform-site-api/compare/3.0.0...3.0.1)

### Changed
* Dump fields by identifier (https://github.com/netgen/ezplatform-site-api/commit/c9b377b0c9e424333c3fcb1abc3c96478918de76)

3.0.0 (25.08.2019)
------------------

[`2.7.2...3.0.0`](https://github.com/netgen/ezplatform-site-api/compare/2.7.2...3.0.0)

### Added
* Add default view templates ([#130](https://github.com/netgen/ezplatform-site-api/pull/130))
* Implement ParamConverters for SiteAPI value objects by @MarioBlazek ([#129](https://github.com/netgen/ezplatform-site-api/pull/129))
* Add routers that generate links for Site API Content and Location by @emodric ([#125](https://github.com/netgen/ezplatform-site-api/pull/125))
* Add support for eZ Platform v3 by @emodric ([#105](https://github.com/netgen/ezplatform-site-api/pull/105), [#123](https://github.com/netgen/ezplatform-site-api/pull/123))
* Allow null value for is_field_empty option ([#120](https://github.com/netgen/ezplatform-site-api/pull/120))
* Allow defining publication date with explicit operators ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))
* Query type condition `creation_date`, replaces `publication_date` ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))
* Allow defining content type and section with explicit operators ([#118](https://github.com/netgen/ezplatform-site-api/pull/118))
* Add a way to render Content view without using a subrequest ([#117](https://github.com/netgen/ezplatform-site-api/pull/117))
* Refactor base Controller ([#115](https://github.com/netgen/ezplatform-site-api/pull/115))
* Implement hinted search result extraction methods ([#112](https://github.com/netgen/ezplatform-site-api/pull/112))
* Add default view matcher, configuring the view with fallback templates by @emodric ([#109](https://github.com/netgen/ezplatform-site-api/pull/109))
* Add missing logger to ImageRuntime ([#106](https://github.com/netgen/ezplatform-site-api/pull/106))
* Prevent site from crashing because of basic Content Field inconsistencies ([#94](https://github.com/netgen/ezplatform-site-api/pull/94))
* Introduce limit parameter to `loadFieldRelations()` method by @mivancic ([#68](https://github.com/netgen/ezplatform-site-api/pull/68))

### Changed
* Minimum PHP version is raised to 7.1
* Minimum eZ Platform version is raised to 2.4
* Allow defining publication date with explicit operators ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))
* Implement Twig runtimes and use Twig namespaces by @emodric ([#60](https://github.com/netgen/ezplatform-site-api/pull/60))
* Mark services as public/private as needed by @emodric ([#59](https://github.com/netgen/ezplatform-site-api/pull/59))

### Removed
This is a major release where all previous deprecations are removed.

Methods:

* `Netgen\EzPlatformSiteApi\API\FilterService::filterContentInfo()`
* `Netgen\EzPlatformSiteApi\API\FindService::findContentInfo()`
* `Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()`
* `Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfoByRemoteId()`
* `Netgen\EzPlatformSiteApi\API\Values\ContentInfo::getLocations()`
* `Netgen\EzPlatformSiteApi\API\Values\ContentInfo::filterLocations()`
* `Netgen\EzPlatformSiteApi\API\FindService::findNodes()`
* `Netgen\EzPlatformSiteApi\API\LoadService::loadNode()`
* `Netgen\EzPlatformSiteApi\API\LoadService::loadNodeByRemoteId()`

Traits:

* `Netgen\EzPlatformSiteApi\Core\Traits\PagerfantaFindTrait`

Classes:

* `Netgen\EzPlatformSiteApi\API\Values\Node`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\BaseAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentInfoSearchAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentInfoSearchHitAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchHitAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchHitAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\NodeSearchAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\NodeSearchHitAdapter`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\Slice`
* `Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\SearchResultExtras`

Other:

* Query type condition `publication_date` is removed, `creation_date` should be used instead ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))
* `content_view` provider for Site API ContentView ([#128](https://github.com/netgen/ezplatform-site-api/pull/128))

### Fixed
* Fix draft preview ([#131](https://github.com/netgen/ezplatform-site-api/pull/131))
* Fix token not found exception in ContentViewBuilder when used to build own exception page by @emodric ([#126](https://github.com/netgen/ezplatform-site-api/pull/126))

2.7.2 (25.08.2019)
------------------

[`2.7.1...2.7.2`](https://github.com/netgen/ezplatform-site-api/compare/2.7.1...2.7.2)

### Fixed
* Load VersionInfo instead of a full Content ([#127](https://github.com/netgen/ezplatform-site-api/pull/127))
* Token not found exception in ContentViewBuilder by @emodric ([#126](https://github.com/netgen/ezplatform-site-api/pull/126))

2.7.1 (25.07.2019)
------------------

[`2.7.0...2.7.1`](https://github.com/netgen/ezplatform-site-api/compare/2.7.0...2.7.1)

### Added
* Add `creation_date` as an alias of `publication_date` Query Type parameter ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))

### Fixed
* Service `ezpublish.api.repository` should be public to avoid deprecation notice by @emodric ([#108](https://github.com/netgen/ezplatform-site-api/pull/108))
* Allow defining `content_type` and `section` Query Type parameters with explicit operators ([#118](https://github.com/netgen/ezplatform-site-api/pull/118))
* Allow defining `publication_date` Query Type parameter with explicit operators ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))
* Allow using `null` value for `is_field_empty` Query Type parameter ([#120](https://github.com/netgen/ezplatform-site-api/pull/120))

### Deprecated
* `publication_date` Query Type parameter is deprecated for removal in `3.0`, new name `creation_date` should be used instead ([#119](https://github.com/netgen/ezplatform-site-api/pull/119))

2.7.0 (30.06.2019)
------------------

[`2.6.3...2.7.0`](https://github.com/netgen/ezplatform-site-api/compare/2.6.3...2.7.0)

* Deprecates methods and properties from `ContentInfo` ([#87](https://github.com/netgen/ezplatform-site-api/pull/87)):
  * `getLocations()`
  * `getLocations()`
  * `$content`

  `ContentInfo` can be accessed through `Content`, loading it independently was deprecated since 2.2. These methods and properties can be obtained from the `Content` object.
* Adds access to dynamic configuration from query type language expressions ([#96](https://github.com/netgen/ezplatform-site-api/pull/96))
* Adds type-casting query string parameter getters to query type language expressions ([#97](https://github.com/netgen/ezplatform-site-api/pull/97))
* Adds optional definition of allowed values for query string parameters to query type language expressions ([#98](https://github.com/netgen/ezplatform-site-api/pull/98))
* Adds support for `IsEmptyField` criterion in Query Type configuration ([#100](https://github.com/netgen/ezplatform-site-api/pull/100))
* Deprecates from Pagerfanta pagination ([#101](https://github.com/netgen/ezplatform-site-api/pull/101)):
  * `BaseAdapter`
  * `Slice`
  * `SearchResultExtras`

  These have been moved to [netgen/ezplatform-search-extra](https://github.com/netgen/ezplatform-search-extra). Existing `FilterAdapter` and `FindAdapter` will continue working as before.

2.6.3 (03.06.2019)
------------------

[`2.6.2...2.6.3`](https://github.com/netgen/ezplatform-site-api/compare/2.6.2...2.6.3)

* Fix generating Location targets when checking for read/view_embed permissions ([#95](https://github.com/netgen/ezplatform-site-api/pull/95))

2.6.2 (03.05.2019)
------------------

[`2.6.1...2.6.2`](https://github.com/netgen/ezplatform-site-api/compare/2.6.1...2.6.2)

* Use instance of Repository Location to check for read permissions by @MarioBlazek ([#93](https://github.com/netgen/ezplatform-site-api/pull/93))

2.6.1 (02.04.2019)
------------------

[`2.6.0...2.6.1`](https://github.com/netgen/ezplatform-site-api/compare/2.6.0...2.6.1)

* Use `sudo()` to lazy load `Content::$innerContent` in #90
* Support for eZ Platform `2.5` in #92

2.6.0 (19.01.2019)
------------------

[`2.5.5...2.6.0`](https://github.com/netgen/ezplatform-site-api/compare/2.5.5...2.6.0)

* refactored tests for easier maintenance
* new Read the Docs documentation site at https://docs.netgen.io/projects/site-api
* bumped `netgen/ezplatform-search-extra` to `1.5` for `Loading` implementation of the result extractor, which prevents edge-case errors when Solr index is not up to date (https://github.com/netgen/ezplatform-search-extra/pull/15)

2.5.5 (18.09.2018)
------------------

[`2.5.4...2.5.5`](https://github.com/netgen/ezplatform-site-api/compare/2.5.4...2.5.5)

* added `getUserPreferenceService()` to aggregate repository by @emodric (7bd14c01d9192407e60085364de69dcec9dc6d8e)

2.5.4 (03.08.2018)
------------------

[`2.5.3...2.5.4`](https://github.com/netgen/ezplatform-site-api/compare/2.5.3...2.5.4)

* This release fixes a regression introduced in #80 ([#82](https://github.com/netgen/ezplatform-site-api/pull/82))

2.5.3 (03.08.2018)
------------------

[`2.5.2...2.5.3`](https://github.com/netgen/ezplatform-site-api/compare/2.5.2...2.5.3)

* Support for `Section` and `ObjectState` identifiers with QueryTypes ([#78](https://github.com/netgen/ezplatform-site-api/pull/78))
* Fixed Pagerfanta adapters not correctly setting `nbResults` by @mivancic ([#79](https://github.com/netgen/ezplatform-site-api/pull/79))
* Removed usage of deprecated Pagerfanta adapters by @mivancic ([#80](https://github.com/netgen/ezplatform-site-api/pull/80))

2.5.2 (29.06.2018)
------------------

[`2.5.1...2.5.2`](https://github.com/netgen/ezplatform-site-api/compare/2.5.1...2.5.2)

* Added `getNotificationService()` to aggregate repository by @emodric (41151c51de1379b774a0ca95445aa8872c93a26d)

2.5.1 (16.06.2018)
------------------

[`2.5.0...2.5.1`](https://github.com/netgen/ezplatform-site-api/compare/2.5.0...2.5.1)

* Added `getBookmarkService()` to aggregate repository by @emodric ([#77](https://github.com/netgen/ezplatform-site-api/pull/77))

2.5.0 (04.06.2018)
------------------

[`2.4.4...2.5.0`](https://github.com/netgen/ezplatform-site-api/compare/2.4.4...2.5.0)

* support for QueryTypes ([#70](https://github.com/netgen/ezplatform-site-api/pull/70))
* minor doc correction by @wizhippo ([#71](https://github.com/netgen/ezplatform-site-api/pull/71))
* improved test coverage ([#73](https://github.com/netgen/ezplatform-site-api/pull/73))
* exposed facets on Pagerfanta adapters by @wizhippo ([#74](https://github.com/netgen/ezplatform-site-api/pull/74))
* rewrite of Pagerfanta adapters ([#76](https://github.com/netgen/ezplatform-site-api/pull/76))
* deprecation of `PagerfantaFindTrait` and old Pagerfanta adapters:
  * `ContentSearchAdapter`
  * `ContentSearchFilterAdapter`
  * `ContentSearchHitAdapter`
  * `LocationSearchAdapter`
  * `LocationSearchFilterAdapter`
  * `LocationSearchHitAdapter`

2.4.4 (21.03.2018)
------------------

[`2.4.3...2.4.4`](https://github.com/netgen/ezplatform-site-api/compare/2.4.3...2.4.4)

* Fix building repository after https://github.com/ezsystems/ezpublish-kernel/pull/2253

2.4.3 (22.02.2018)
------------------

[`2.4.2...2.4.3`](https://github.com/netgen/ezplatform-site-api/compare/2.4.2...2.4.3)

* Fix using `ContentViewBuilder` when dealing with closure controllers

2.4.2 (15.02.2018)
------------------

[`2.4.1...2.4.2`](https://github.com/netgen/ezplatform-site-api/compare/2.4.1...2.4.2)

* Fix the method name in `ContentValueView` interface

2.4.1 (02.02.2018)
------------------

[`2.4.0...2.4.1`](https://github.com/netgen/ezplatform-site-api/compare/2.4.0...2.4.1)

* Fix filter service always using anonymous users ([#64](https://github.com/netgen/ezplatform-site-api/pull/64), [#67](https://github.com/netgen/ezplatform-site-api/pull/67))

2.4.0 (22.01.2018)
------------------

[`2.3.2...2.4.0`](https://github.com/netgen/ezplatform-site-api/compare/2.3.2...2.4.0)

* Implemented Pagerfanta trait ([#52](https://github.com/netgen/ezplatform-site-api/pull/52))
* Removed support for PHP 5.5 ([#65](https://github.com/netgen/ezplatform-site-api/pull/65))
* Enabled PHPUnit 6 for tests ([#63](https://github.com/netgen/ezplatform-site-api/pull/63))
* Improvements to docs ([#62](https://github.com/netgen/ezplatform-site-api/pull/62))

2.3.2 (02.02.2018)
------------------

[`2.3.1...2.3.2`](https://github.com/netgen/ezplatform-site-api/compare/2.3.1...2.3.2)

* Fix filter service always using anonymous users ([#64](https://github.com/netgen/ezplatform-site-api/pull/64), [#67](https://github.com/netgen/ezplatform-site-api/pull/67))

2.3.1 (17.01.2018)
------------------

[`2.3.0...2.3.1`](https://github.com/netgen/ezplatform-site-api/compare/2.3.0...2.3.1)

* Fix anonymous users do not have access to content.owner property ([#61](https://github.com/netgen/ezplatform-site-api/pull/61), [#66](https://github.com/netgen/ezplatform-site-api/pull/66))

2.3.0 (14.12.2017)
------------------

[`2.2.1...2.3.0`](https://github.com/netgen/ezplatform-site-api/compare/2.2.1...2.3.0)

* `Content`, `ContentInfo`, `Field` and `Location` now implement `__debugInfo()` method that controls which
  properties are shown when the object is dumped. Through it, recursion and tree traversal are avoided,
  which will provide cleaner output when dumping the objects for debugging purpose. ([#50](https://github.com/netgen/ezplatform-site-api/pull/50))
* Content now implements `$owner` and `$innerOwnerUser` lazy-loaded properties. ([#51](https://github.com/netgen/ezplatform-site-api/pull/51))
* More tests by @MarioBlazek ([#53](https://github.com/netgen/ezplatform-site-api/pull/53))
* We now support eZ Platform Kernel 7.0 beta by @emodric ([#54](https://github.com/netgen/ezplatform-site-api/pull/54))
* Some regular expressions to ease migration by @MarioBlazek ([#55](https://github.com/netgen/ezplatform-site-api/pull/55))
* Adapters from eZ Platform Kernel `SearchService` interface to `FindService` and `FilterService` by @emodric ([#57](https://github.com/netgen/ezplatform-site-api/pull/57))
* Support for simple forward relations with `RelationsService` and new methods on `Content` object ([#42](https://github.com/netgen/ezplatform-site-api/pull/42))

2.2.1 (02.02.2018)
------------------

[`2.2.0...2.2.1`](https://github.com/netgen/ezplatform-site-api/compare/2.2.0...2.2.1)

* * Fix filter service always using anonymous users ([#64](https://github.com/netgen/ezplatform-site-api/pull/64), [#67](https://github.com/netgen/ezplatform-site-api/pull/67))

2.2.0 (05.10.2017)
------------------

[`2.1.2...2.2.0`](https://github.com/netgen/ezplatform-site-api/compare/2.1.2...2.2.0)

* Introduces lazy loading of `Content` fields, meaning that fields will be transparently loaded only
if accessed
* Introduces lazy loading of `ContentInfo` when accessed from `Content` or `Location`
* Deprecates all methods to obtain `ContentInfo` object (to be removed in 3.0):
  * `LoadService::loadContentInfo()`
  * `LoadService::loadContentInfoByRemoteId()`
  * `FilterService::filterContentInfo()`
  * `FindService::findContentInfo()`

  The intention behind this is that, with lazy loading of `Content` fields, `Content` takes over the
  role of `ContentInfo`. It basically behaves the same until the fields are accessed, so you don't
  need to think about it. 

  Note that `ContentInfo` itself is not deprecated, for the sole reason of keeping Site API in line
  with Repository API. With 3.0 the only way to access `ContentInfo` object will be through
  aggregation in `Content` and `Location` objects.
* Deprecates ContentInfo Pagerfanta search adapters (to be removed in 3.0):
  * `ContentInfoSearchAdapter`
  * `ContentInfoSearchHitAdapter`
* Fixes https://github.com/netgen/ezplatform-site-api/issues/48: Mapping a field takes the wrong ID

2.1.2 (02.02.2018)
------------------

[`2.1.1...2.1.2`](https://github.com/netgen/ezplatform-site-api/compare/2.1.1...2.1.2)

* Fix filter service always using anonymous users ([#64](https://github.com/netgen/ezplatform-site-api/pull/64), [#67](https://github.com/netgen/ezplatform-site-api/pull/67))

2.1.1 (07.09.2017)
------------------

[`2.1.0...2.1.1`](https://github.com/netgen/ezplatform-site-api/compare/2.1.0...2.1.1)

* Fix an issue where default value for `ezsettings.default.ngcontent_view` would overwrite any existing value on eZ kernel 6.11+

2.1.0 (31.07.2017)
------------------

[`2.0.1...2.1.0`](https://github.com/netgen/ezplatform-site-api/compare/2.0.1...2.1.0)

* Introduces `FilterService`, providing search on top of Legacy Search Engine (it doesn't depend
on indexing for data to became available in search)
* Introduces `Settings`, containing all user-defined settings used by the Site API
* Adds new methods and (lazy-loaded) properties to API entities, enabling simple Content structure
traversal from both PHP and Twig without writing boilerplate code:
  * `Location::$content`
  * `Location::$parent`
  * `Location::getChildren()`
  * `Location::filterChildren()`
  * `Location::getSiblings()`
  * `Location::filterSiblings()`
  * `Content::$mainLocation`
  * `Content::getLocations()`
  * `Content::filterLocations()`
  * `ContentInfo::$content`
  * `ContentInfo::$mainLocation`
  * `ContentInfo::getLocations()`
  * `ContentInfo::filterLocations()`
* Deprecates `Node` object and corresponding methods and classes (to be removed in 3.0), since `Content` is now
available as lazy-loaded property of `Location`:
  * `Node`
  * `LoadService::loadNode()`
  * `LoadService::loadNodeByRemoteId()`
  * `FindService::findNodes()`
  * `NodeSearchAdapter`
  * `NodeSearchHitAdapter`
* Updates coding style
* Adds `SearchResultExtractorTrait`, providing a way to extract value objects from search result

2.0.1 (26.04.2017)
------------------

[`2.0.0...2.0.1`](https://github.com/netgen/ezplatform-site-api/compare/2.0.0...2.0.1)

* Fix EZP-27237: fixed wrong content loading logic in ContentViewBuilder
* Improvements to tests
* Remove unneeded ContentTrait

2.0.0 (30.01.2017)
------------------

[`1.0.1...2.0.0`](https://github.com/netgen/ezplatform-site-api/compare/1.0.1...2.0.0)

* It is now possible to use both `ez_content:viewAction` & `ng_content:viewAction` controllers, side by side, by separating Site API specific `content_view` rules to a separate config named `ngcontent_view`

1.0.1 (13.12.2016)
------------------

[`1.0.0...1.0.1`](https://github.com/netgen/ezplatform-site-api/compare/2.3.0...2.3.1)

* Allow eZ value objects to be transferred to `ng_content:viewAction`

1.0.0 (15.09.2016)
------------------

* Initial release
