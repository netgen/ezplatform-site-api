``state``
~~~~~~~~~

Defines ObjectState of the Content by the ObjectStateGroup and ObjectState identifiers.

.. note:: Content can only exist in single ObjectState from the same ObjectStateGroup.

- **value type**: ``string`` ObjectState identifier
- **value format**: ``single``
- **operators**: ``eq``
- **target**: ``string`` ObjectStateGroup identifier
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    # identical to the example below
    state:
        ez_lock: not_locked

.. code-block:: yaml

    state:
        ez_lock:
            eq: not_locked

.. code-block:: yaml

    # multiple states are combined with logical AND
    # identical to the example below
    state:
        ez_lock: locked
        approval: rejected

.. code-block:: yaml

    state:
        ez_lock:
            eq: locked
        approval:
            eq: rejected
