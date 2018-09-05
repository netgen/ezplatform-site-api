``priority``
~~~~~~~~~~~~

Defines the priority of the Location.

- **value type**: ``integer``
- **value format**: ``single``
- **operators**: ``gt``, ``gte``, ``lt``, ``lte``, ``between``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # multiple operators are combined with logical AND
    depth:
        gt: 4
        lte: 8

.. code-block:: yaml

    depth:
        between: [4, 7]
