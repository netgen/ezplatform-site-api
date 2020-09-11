``visible``
~~~~~~~~~~~

Defines whether returned Content objects are visible or not.
Use ``true`` to get visible Content objects, ``false`` to get hidden Content objects and ``null`` to get both (which is also the default behaviour).

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    visible: false

.. code-block:: yaml

    visible:
        eq: false

.. code-block:: yaml

    # get both visible and hidden Content objects, which also the default behaviour
    visible: ~
