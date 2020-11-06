``visible``
~~~~~~~~~~~

Defines whether returned Locations are visible or not.
Use ``true`` to get visible Locations, ``false`` to get hidden Locations and ``null`` to get both (which is also the default behaviour).

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: ``null``

Examples:

.. code-block:: yaml

    # identical to the example below
    visible: false

.. code-block:: yaml

    visible:
        eq: false

.. code-block:: yaml

    # get both visible and hidden Locations, which also the default behaviour
    visible: ~
