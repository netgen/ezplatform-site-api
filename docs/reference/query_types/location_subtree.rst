Location subtree Query Type
================================================================================

This Query Type is used to build queries that fetch from the Location subtree.

+-------------+------------------------------------------------------------------------------+
| Identifier  | ``SiteAPI:Location/Subtree``                                                 |
+-------------+------------------------------------------------------------------------------+
| Own         | - `exclude_self`_                                                            |
| conditions  | - `location`_                                                                |
|             | - `relative_depth`_                                                          |
+-------------+------------------------------------------------------------------------------+
| Inherited   | - `depth`_                                                                   |
| Location    | - `main`_                                                                    |
| conditions  | - `priority`_                                                                |
|             | - `visible`_                                                                 |
+-------------+------------------------------------------------------------------------------+
| Common      | - `content_type`_                                                            |
| Content     | - `field`_                                                                   |
| conditions  | - `publication_date`_                                                        |
|             | - `section`_                                                                 |
|             | - `state`_                                                                   |
+-------------+------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                   |
| query       | - `offset`_                                                                  |
| parameters  | - `sort`_                                                                    |
+-------------+------------------------------------------------------------------------------+

.. | Class       | :class:`Netgen\\EzPlatformSiteApi\\Core\\Site\\QueryType\\Location\\Subtree`   |
.. +-------------+------------------------------------------------------------------------------+

Examples
--------------------------------------------------------------------------------

Subtree of the ``calendar`` type Location contains ``event`` type Locations. On the full view for
``calendar`` fetch all pending events from its subtree up to depth of 3, sort them by their start
date and paginate them by 10 per page using URL query parameter ``page``:

.. code-block:: yaml

    ezpublish:
        system:
            frontend_group:
                ngcontent_view:
                    full:
                        calendar:
                            template: '@ezdesign/content/full/calendar.html.twig'
                            match:
                                Identifier\ContentType: calendar
                            queries:
                                pending_events:
                                    query_type: SiteAPI:Content/Location/Subtree
                                    max_per_page: 10
                                    page: '@=queryParam("page", 1)'
                                    parameters:
                                        content_type: event
                                        relative_depth:
                                            lte: 3
                                        field:
                                            start_date:
                                                gt: '@=timestamp("today")'
                                        sort: field/event/start_date asc

.. code-block:: twig

    <h3>Pending events:</h3>

    <ul>
    {% for event in ng_query( 'pending_events' ) %}
        <li>{{ event.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( children, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``exclude_self``
~~~~~~~~~~~~~~~~

Defines whether to include Location defined by the ``location`` condition in the result set.

- **value type**: ``boolean``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``false``
- **default**: ``true``

Examples:

.. code-block:: yaml

    # do not include the subtree root Location, this is also default behaviour
    exclude_self: true

.. code-block:: yaml

    # include the subtree root Location
    exclude_self: false

``location``
~~~~~~~~~~~~

Defines the root Location of the Location subtree.

.. note:: This condition is required.

- **value type**: ``Location``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

  If used through view builder configuration, value will be automatically set to the ``Location``
  instance resolved by the view builder.

Examples:

.. code-block:: yaml

    # this is also automatically set when using from view builder configuration
    location: '@=location'

.. code-block:: yaml

    # fetch from subtree of the parent Location
    location: '@=location.parent'

.. code-block:: yaml

    # fetch from subtree of the parent Location's parent Location
    location: '@=location.parent.parent'

``relative_depth``
~~~~~~~~~~~~~~~~~~

Defines depth of the Location in the tree relative to the Location defined by ``location``
condition.

- **value type**: ``integer``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``, ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    depth: 2

.. code-block:: yaml

    depth:
        eq: 2

.. code-block:: yaml

    # identical to the example below
    depth: [2, 3]

.. code-block:: yaml

    depth:
        in: [2, 3]

.. code-block:: yaml

    # multiple operators are combined with logical AND
    depth:
        in: [2, 3]
        gt: 1
        lte: 3

.. code-block:: yaml

    depth:
        between: [2, 4]

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/depth.rst.inc
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
