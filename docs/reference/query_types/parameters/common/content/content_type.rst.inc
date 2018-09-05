``content_type``
~~~~~~~~~~~~~~~~

Defines ContentType of the Content by the identifier.

- **value type**: ``string``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``
- **target**: ``string`` ContentType identifier
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    content_type: article

.. code-block:: yaml

    content_type:
        eq: article


.. code-block:: yaml

    # identical to the example below
    content_type: [image, video]

.. code-block:: yaml

    content_type:
        in: [image, video]
