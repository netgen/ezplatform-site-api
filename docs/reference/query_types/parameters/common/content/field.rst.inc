``field``
~~~~~~~~~

Defines conditions on Content fields.

- **value type**: ``integer``, ``string``, ``boolean``
- **value format**: ``single``, ``array``
- **operators**: ``eq``, ``in``, ``gt``, ``gte``, ``lt``, ``lte``, ``between``, ``like``, ``contains``
- **target**: ``string`` Field identifier
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    field:
        date_field:
            not:
                gt: 'today +5 days'
        price:
            between: [100, 200]
            not: 155
