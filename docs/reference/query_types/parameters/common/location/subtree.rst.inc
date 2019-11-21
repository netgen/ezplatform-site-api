``subtree``
~~~~~~~~~~~

- **value type**: ``string``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``
- **target**: none
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    subtree: '/1/2/42'

.. code-block:: yaml

    subtree:
        eq: '/1/2/42'

.. code-block:: yaml

    # identical to the example below
    subtree: ['/1/2/5', '/1/2/24', '/1/2/42']

.. code-block:: yaml

    subtree:
        in: ['/1/2/5', '/1/2/24', '/1/2/42']
