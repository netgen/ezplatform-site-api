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
| conditions  | - `is_field_empty`_                                                          |
|             | - `creation_date`_                                                           |
|             | - `modification_date`_                                                       |
|             | - `section`_                                                                 |
|             | - `state`_                                                                   |
+-------------+------------------------------------------------------------------------------+
| Common      | - `limit`_                                                                   |
| query       | - `offset`_                                                                  |
| parameters  | - `sort`_                                                                    |
+-------------+------------------------------------------------------------------------------+

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
                                    query_type: SiteAPI:Location/Subtree
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

    {% set events = ng_query( 'pending_events' ) %}

    <h3>Pending events</h3>

    <ul>
    {% for event in events %}
        <li>{{ event.content.name }}</li>
    {% endfor %}
    </ul>

    {{ pagerfanta( events, 'twitter_bootstrap' ) }}

Own conditions
--------------------------------------------------------------------------------

``exclude_self``
~~~~~~~~~~~~~~~~

Defines whether to include Location defined by the ``location`` condition in the result set.
If ``null`` is used as a value, the condition won't be added.

- **value type**: ``boolean``, ``null``
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

.. note::

  This condition is required. It's also automatically set to the ``Location`` instance resolved by
  the view builder if the query is defined in the view builder configuration.

- **value type**: ``Location``
- **value format**: ``single``
- **operators**: none
- **target**: none
- **required**: ``true``
- **default**: not defined

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
If ``null`` is used as a value, the condition won't be added.

- **value type**: ``integer``, ``null``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``, ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    relative_depth: 2

.. code-block:: yaml

    relative_depth:
        eq: 2

.. code-block:: yaml

    # identical to the example below
    relative_depth: [2, 3]

.. code-block:: yaml

    relative_depth:
        in: [2, 3]

.. code-block:: yaml

    # multiple operators are combined with logical AND
    relative_depth:
        in: [2, 3]
        gt: 1
        lte: 3

.. code-block:: yaml

    relative_depth:
        between: [2, 4]

Inherited Location conditions
--------------------------------------------------------------------------------
.. include:: /reference/query_types/parameters/common/location/depth.rst.inc
.. include:: /reference/query_types/parameters/common/location/main.rst.inc
.. include:: /reference/query_types/parameters/common/location/priority.rst.inc
.. include:: /reference/query_types/parameters/common/location/visible.rst.inc

.. include:: /reference/query_types/parameters/common_content_parameters.rst.inc
.. include:: /reference/query_types/parameters/common_query_parameters.rst.inc
