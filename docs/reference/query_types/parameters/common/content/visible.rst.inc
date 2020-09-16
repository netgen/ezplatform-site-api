``visible``
~~~~~~~~~~~

Defines condition on visibility status of the returned Content items. Use ``true`` to get back only visible Content (the
default behaviour), ``false`` to get back only hidden Content and ``null`` to get both.

.. warning:: Default value of this condition can be affected by the siteaccess configuration.

- **value type**: ``boolean``, ``null``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: none
- **required**: ``false``
- **default**: as defined by ``show_hidden_items`` :ref:`configuration option<show_hidden_items_configuration>` (the
  opposite from it's value, resolved as ``visible: true`` by default)

Examples:

.. code-block:: yaml

    # identical to the example below
    visible: false

.. code-block:: yaml

    visible:
        eq: false

.. code-block:: yaml

    # get both visible and hidden Content objects
    visible: ~
