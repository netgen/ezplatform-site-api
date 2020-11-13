``parent_location_id``
~~~~~~~~~~~~~~~~~~~~~~

Defines Location's parent Location ID.
If ``null`` is used as a value, the condition won't be added.

- **value type**: ``integer``, ``string``, ``null``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    parent_location_id: 42

.. code-block:: yaml

    parent_location_id:
        eq: 42

.. code-block:: yaml

    # identical to the example below
    parent_location_id: [11, 24, 42]

.. code-block:: yaml

    parent_location_id:
        in: [11, 24, 42]
