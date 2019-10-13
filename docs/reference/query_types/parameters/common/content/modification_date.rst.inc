``modification_date``
~~~~~~~~~~~~~~~~~~~~~

Defines the modification (last publication) date of the Content as a timestamp or time string as
accepted by PHP function `strtotime <https://www.php.net/manual/en/function.strtotime.php>`_.

- **value type**: ``integer``, ``string``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``, ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    modification_date: 1535117737

.. code-block:: yaml

    modification_date:
        eq: 1535117737

.. code-block:: yaml

    # identical to the example below
    modification_date: [1435117737, 1535117737]

.. code-block:: yaml

    modification_date:
        in: [1435117737, 1535117737]

.. code-block:: yaml

    # multiple operators are combined with logical AND
    modification_date:
        gt: '29 June 1991'
        lte: '5 August 1995'

.. code-block:: yaml

    modification_date:
        gt: 'today'

.. code-block:: yaml

    modification_date:
        between: ['-1 week 2 days 4 hours 2 seconds', 'today']
