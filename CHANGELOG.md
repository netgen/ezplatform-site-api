eZ Platform Site API changelog
==============================

2.4.3 (22.02.2018)
------------------

* Fix using `ContentViewBuilder` when dealing with closure controllers

2.4.2 (15.02.2018)
------------------

* Fix the method name in `ContentValueView` interface

2.4.1 (02.02.2018)
------------------

* Fix filter service always using anonymous users (#64, #67)

2.4.0 (22.01.2018)
------------------

* Implemented Pagerfanta trait (#52)
* Removed support for PHP 5.5 (#65)
* Enabled PHPUnit 6 for tests (#63)
* Improvements to docs (#62)

2.3.2 (02.02.2018)
------------------

* Fix filter service always using anonymous users (#64, #67)

2.3.1 (17.01.2018)
------------------

* Fix anonymous users do not have access to content.owner property (#61, #66)

2.3.0 (14.12.2017)
------------------

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

* * Fix filter service always using anonymous users (#64, #67)

2.2.0 (05.10.2017)
------------------

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

* Fix filter service always using anonymous users (#64, #67)

2.1.1 (07.09.2017)
------------------

* Fix an issue where default value for `ezsettings.default.ngcontent_view` would overwrite any existing value on eZ kernel 6.11+

2.1.0 (31.07.2017)
------------------

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

* Fix EZP-27237: fixed wrong content loading logic in ContentViewBuilder
* Improvements to tests
* Remove unneeded ContentTrait

2.0.0 (30.01.2017)
------------------

* It is now possible to use both `ez_content:viewAction` & `ng_content:viewAction` controllers, side by side, by separating Site API specific `content_view` rules to a separate config named `ngcontent_view`

1.0.1 (13.12.2016)
------------------

* Allow eZ value objects to be transferred to `ng_content:viewAction`

1.0.0 (15.09.2016)
------------------

* Initial release
