``section``
~~~~~~~~~~~

Defines Section of the Content by the identifier.

- **value type**: ``string``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    section: standard

.. code-block:: yaml

    section:
        eq: standard


.. code-block:: yaml

    # identical to the example below
    section: [standard, restricted]

.. code-block:: yaml

    section:
        in: [standard, restricted]
