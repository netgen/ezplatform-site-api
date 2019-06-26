``is_field_empty``
~~~~~~~~~~~~~~~~~~

Defines conditions on whether the Content fields are empty or not.

.. note::

  ``IsEmptyField`` criterion is upported only by Solr search engine, so this condition can be
  used only with the ``FindService``. In order to use it configure the query with parameter ``use_filter``
  set to ``false``.

- **value type**: ``boolean``
- **value format**: ``single``
- **operators**: ``eq``
- **target**: ``string`` Field identifier
- **required**: ``false``
- **default**: not defined

Examples:

.. code-block:: yaml

    is_field_empty:
        image: false
        video: true
