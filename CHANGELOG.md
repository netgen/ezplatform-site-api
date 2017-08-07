eZ Platform Site API changelog
==============================

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
