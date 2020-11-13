``main``
~~~~~~~~

Defines whether returned Locations are main Locations or not.
Use ``true`` to get main Locations, ``false`` to get non-main Locations and ``null`` to get both (which is also the default behaviour).

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: ``not defined``

Examples:

.. code-block:: yaml

    # identical to the example below
    main: true

.. code-block:: yaml

    main:
        eq: true

.. code-block:: yaml

    # get both main and non-main Locations, which is also the default behaviour
    main: ~
