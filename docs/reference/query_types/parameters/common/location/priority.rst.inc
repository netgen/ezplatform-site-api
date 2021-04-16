``priority``
~~~~~~~~~~~~

Defines the priority of the Location.
If ``null`` is used as a value, the condition won't be added.

- **value type**: ``integer``, ``null``
- **value format**: ``single``
- **operators**: ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # multiple operators are combined with logical AND
    priority:
        gt: 4
        lte: 8

.. code-block:: yaml

    priority:
        between: [4, 7]
