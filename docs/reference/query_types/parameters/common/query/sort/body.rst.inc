Here you can use any SortClause implementation or a :ref:`Site API Location object<location_object>`.
Using a Location object is the same a using sort clauses that are defined on it:

.. code-block:: yaml

    sort: "@=location"

The example above is a shortcut to the example below:

.. code-block:: yaml

    sort: "@=location.innerLocation.getSortClauses()"

If you define your query in the view configuration, you won't be able to directly instantiate a
SortClause there. For that reason we provide a way to define the sort clause as a string instead.
With this format a subset of commonly used SortClauses is supported. Sort direction is defined as
``asc`` for ascending and ``desc`` for  descending. In can be omitted, in which case it will default
to ``asc``.

Strings can be used to define multiple sort clauses through an array of definitions:

.. code-block:: yaml

    sort:
        - depth asc
        - modified desc

Following sort clauses are available through string definition:

.. contents::
    :depth: 1
    :local:

Location depth
~~~~~~~~~~~~~~

String ``depth`` enables sorting by Location's depth:

.. code-block:: yaml

    sort: depth

.. code-block:: yaml

    sort: depth asc

.. code-block:: yaml

    sort: depth desc

Content Field
~~~~~~~~~~~~~

String in form of of ``field/[content_type]/[field]`` enables sorting by any Content Field. For
example by Field with identifier ``title`` in ContentType with identifier ``article``:

.. code-block:: yaml

    sort: field/article/title

.. code-block:: yaml

    sort: field/article/title asc

.. code-block:: yaml

    sort: field/article/title desc

Content modification date
~~~~~~~~~~~~~~~~~~~~~~~~~

String ``modified`` enables sorting by the Content modification date:

.. code-block:: yaml

    sort: modified

.. code-block:: yaml

    sort: modified asc

.. code-block:: yaml

    sort: modified desc

Content name
~~~~~~~~~~~~

String ``name`` enables sorting by the Content name:

.. code-block:: yaml

    sort: name

.. code-block:: yaml

    sort: name asc

.. code-block:: yaml

    sort: name desc

Location priority
~~~~~~~~~~~~~~~~~

String ``priority`` enables sorting by the Location priority:

.. code-block:: yaml

    sort: priority

.. code-block:: yaml

    sort: priority asc

.. code-block:: yaml

    sort: priority desc

Content publication date
~~~~~~~~~~~~~~~~~~~~~~~~

String ``published`` enables sorting by the Content publication/creation date:

.. code-block:: yaml

    sort: published

.. code-block:: yaml

    sort: published asc

.. code-block:: yaml

    sort: published desc
