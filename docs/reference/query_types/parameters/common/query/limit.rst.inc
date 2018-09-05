``limit``
~~~~~~~~~

Defines the maximum number of items to return.

.. note:: This parameter will not be used if you execute the query from Twig using ``ng_query`` function.
          In that case ``Pargerfanta`` pager is used with semantic parameters ``page`` and ``max_per_page``.
          To execute the query directly use ``ng_raw_query`` Twig function instead.

- **value type**: ``integer``
- **value format**: ``single``
- **required**: ``false``
- **default**: ``25``

Examples:

.. code-block:: yaml

    limit: 10
