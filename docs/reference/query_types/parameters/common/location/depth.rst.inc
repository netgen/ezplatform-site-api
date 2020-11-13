``depth``
~~~~~~~~~

Defines absolute depth of the Location in the tree.
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
    depth: 3

.. code-block:: yaml

    depth:
        eq: 3

.. code-block:: yaml

    # identical to the example below
    depth: [3, 4, 8]

.. code-block:: yaml

    depth:
        in: [3, 4, 8]

.. code-block:: yaml

    # multiple operators are combined with logical AND
    depth:
        in: [3, 4, 5]
        gt: 4
        lte: 8

.. code-block:: yaml

    depth:
        between: [4, 7]
