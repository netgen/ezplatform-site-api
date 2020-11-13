``offset``
~~~~~~~~~~

Defines the offset for search hits, used for paging the results.
If ``null`` is used as a value, the offset will be set to the default value.

.. note:: This parameter will not be used if you execute the query from Twig using ``ng_query`` function.
          In that case ``Pargerfanta`` pager is used with semantic parameters ``page`` and ``max_per_page``.
          To execute the query directly use ``ng_raw_query`` Twig function instead.

- **value type**: ``integer``, ``null``
- **value format**: ``single``
- **required**: ``false``
- **default**: ``0``

Examples:

.. code-block:: yaml

    offset: 20
