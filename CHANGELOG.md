eZ Platform Site API changelog
==============================

2.7.2 (25.08.2019)
------------------

[`2.7.1...2.7.2`](https://github.com/netgen/ezplatform-site-api/compare/2.7.1...2.7.2)

### Fixed
* Load VersionInfo instead of a full Content (#127)
* Token not found exception in ContentViewBuilder by @emodric (#126)

2.7.1 (25.07.2019)
------------------

[`2.7.0...2.7.1`](https://github.com/netgen/ezplatform-site-api/compare/2.7.0...2.7.1)

### Added
* Add `creation_date` as an alias of `publication_date` Query Type parameter (#119)

### Fixed
* Service `ezpublish.api.repository` should be public to avoid deprecation notice by @emodric (#108)
* Allow defining `content_type` and `section` Query Type parameters with explicit operators (#118)
* Allow defining `publication_date` Query Type parameter with explicit operators (#119)
* Allow using `null` value for `is_field_empty` Query Type parameter (#120)

### Deprecated
* `publication_date` Query Type parameter is deprecated for removal in `3.0`, new name `creation_date` should be used instead (#119)

2.7.0 (30.06.2019)
------------------

[`2.6.3...2.7.0`](https://github.com/netgen/ezplatform-site-api/compare/2.6.3...2.7.0)

* Deprecates methods and properties from `ContentInfo` (#87):
  * `getLocations()`
  * `getLocations()`
  * `$content`

  `ContentInfo` can be accessed through `Content`, loading it independently was deprecated since 2.2. These methods and properties can be obtained from the `Content` object.
* Adds access to dynamic configuration from query type language expressions (#96)
* Adds type-casting query string parameter getters to query type language expressions (#97)
* Adds optional definition of allowed values for query string parameters to query type language expressions (#98)
* Adds support for `IsEmptyField` criterion in Query Type configuration (#100)
* Deprecates from Pagerfanta pagination (#101):
  * `BaseAdapter`
  * `Slice`
  * `SearchResultExtras`

  These have been moved to [netgen/ezplatform-search-extra](https://github.com/netgen/ezplatform-search-extra). Existing `FilterAdapter` and `FindAdapter` will continue working as before.

2.6.3 (03.06.2019)
------------------

[`2.6.2...2.6.3`](https://github.com/netgen/ezplatform-site-api/compare/2.6.2...2.6.3)

* Fix generating Location targets when checking for read/view_embed permissions (#95)

2.6.2 (03.05.2019)
------------------

[`2.6.1...2.6.2`](https://github.com/netgen/ezplatform-site-api/compare/2.6.1...2.6.2)

* Use instance of Repository Location to check for read permissions by @MarioBlazek (#93)

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

* This release fixes a regression introduced in #80 (#82)

2.5.3 (03.08.2018)
------------------

[`2.5.2...2.5.3`](https://github.com/netgen/ezplatform-site-api/compare/2.5.2...2.5.3)

* Support for `Section` and `ObjectState` identifiers with QueryTypes (#78)
* Fixed Pagerfanta adapters not correctly setting `nbResults` by @mivancic (#79)
* Removed usage of deprecated Pagerfanta adapters by @mivancic (#80)

2.5.2 (29.06.2018)
------------------

[`2.5.1...2.5.2`](https://github.com/netgen/ezplatform-site-api/compare/2.5.1...2.5.2)

* Added `getNotificationService()` to aggregate repository by @emodric (41151c51de1379b774a0ca95445aa8872c93a26d)

2.5.1 (16.06.2018)
------------------

[`2.5.0...2.5.1`](https://github.com/netgen/ezplatform-site-api/compare/2.5.0...2.5.1)

* Added `getBookmarkService()` to aggregate repository by @emodric (#77)

2.5.0 (04.06.2018)
------------------

[`2.4.4...2.5.0`](https://github.com/netgen/ezplatform-site-api/compare/2.4.4...2.5.0)

* support for QueryTypes (#70)
* minor doc correction by @wizhippo (#71)
* improved test coverage (#73)
* exposed facets on Pagerfanta adapters by @wizhippo (#74)
* rewrite of Pagerfanta adapters (#76)
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

* Fix filter service always using anonymous users (#64, #67)

2.4.0 (22.01.2018)
------------------

[`2.3.2...2.4.0`](https://github.com/netgen/ezplatform-site-api/compare/2.3.2...2.4.0)

* Implemented Pagerfanta trait (#52)
* Removed support for PHP 5.5 (#65)
* Enabled PHPUnit 6 for tests (#63)
* Improvements to docs (#62)

2.3.2 (02.02.2018)
------------------

[`2.3.1...2.3.2`](https://github.com/netgen/ezplatform-site-api/compare/2.3.1...2.3.2)

* Fix filter service always using anonymous users (#64, #67)

2.3.1 (17.01.2018)
------------------

[`2.3.0...2.3.1`](https://github.com/netgen/ezplatform-site-api/compare/2.3.0...2.3.1)

* Fix anonymous users do not have access to content.owner property (#61, #66)

2.3.0 (14.12.2017)
------------------

[`2.2.1...2.3.0`](https://github.com/netgen/ezplatform-site-api/compare/2.2.1...2.3.0)

* `Content`, `ContentInfo`, `Field` and `Location` now implement `__debugInfo()` method that controls which
  properties are shown when the object is dumped. Through it, recursion and tree traversal are avoided,
  which will provide cleaner output when dumping the objects for debugging purpose. (#50)
* Content now implements `$owner` and `$innerOwnerUser` lazy-loaded properties. (#51)
* More tests by @MarioBlazek (#53)
* We now support eZ Platform Kernel 7.0 beta by @emodric (#54)
* Some regular expressions to ease migration by @MarioBlazek (#55)
* Adapters from eZ Platform Kernel `SearchService` interface to `FindService` and `FilterService` by @emodric (#57)
* Support for simple forward relations with `RelationsService` and new methods on `Content` object (#42)

2.2.1 (02.02.2018)
------------------

[`2.2.0...2.2.1`](https://github.com/netgen/ezplatform-site-api/compare/2.2.0...2.2.1)

* * Fix filter service always using anonymous users (#64, #67)

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

* Fix filter service always using anonymous users (#64, #67)

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
