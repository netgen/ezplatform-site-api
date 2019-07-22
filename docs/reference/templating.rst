Templating
==========

Site API objects are used directly in the templates. Below you will find examples for the most
common use cases. Objects are documented in more detail on :doc:`Objects reference </reference/objects>` documentation page.

Site API provides two Twig functions for content rendering:

- ``ng_view_content``

  .. warning::

    Twig function ``ng_view_content`` is considered to be experimental. Once the correct behavior is
    confirmed in practice, this warning will be removed.

  This function provides a way to render Content view without executing a subrequest. The idea is
  to avoid bad performance of subrequests in debug mode. Because of profiling that is active in
  debug mode, having a lots of subrequests on a page can significantly affect performance. Since for
  a large part of use cases it's not really necessary to render Content views through a subrequest,
  having an alternative way to render them can improve performance and developer's experience.

  The function can be used for views defined in Site API view configuration and it supports custom
  controllers.

  .. note::

    This function is not a complete replacement for rendering Content views, since it does not
    dispatch MVC events from eZ Publish Kernel, like ``MVCEvents::PRE_CONTENT_VIEW``. For that
    reason it's safe to use only for those views that don't depend on them. This should be the case
    for most of them.

    Depending on the use case, you might be able to replace usage of MVC events with ``ViewEvents``
    from eZ Publish Kernel, which **are** dispatched by ``ng_view_content``.

  The function accepts four parameters, similar as `parameters available for ez_content:viewAction
  controller <https://doc.ezplatform.com/en/latest/guide/templates/#available-arguments>`_:

  1. **required** Content or Location object
  2. **required** string view identifier (e.g. ``line``, ``block``)
  3. **optional** array of parameters, with keys as parameter names and corresponding values as
     parameter values

    Parameters defined through this array will be available as Request attributes and can be passed
    as arguments to the controller action if defined there. Also, parameter with name ``params`` is
    recognized as an array of custom view parameters and will be available in the view object and in
    the rendered template.

  4. **optional** boolean value indicating whether to render the template in the configured layout,
     by default ``false``

  Example usage:

  .. code-block:: twig

    {{ ng_view_content(
        content,
        'line',
        {
            'foo': 'bar',
            'params': {
                'custom': 'view param'
            }
        },
        false
    ) }}

  The example above is intended to replace the following Content view render through the subrequest:

  .. code-block:: twig

    {{ render(
        controller(
            'ng_content:viewAction', {
                'contentId': content.id,
                'viewType': 'line',
                'layout': false,
                'foo': 'bar',
                'params': {
                    'custom': 'view param'
                }
            }
        )
    ) }}

- ``ng_render_field``

  Similar to ``ez_render_field`` from eZ Platform, this function is used to render the Content's
  field using the configured template:

  .. code-block:: twig

    <p>{{ ng_render_field( content.field.body ) }}</p>

- ``ng_image_alias``

  Similar to ``ez_image_alias`` from eZ Platform, this function provides access to the image
  variation of a ``ezimage`` type field:

  .. code-block:: twig

    <img src="{{ ng_image_alias( content.fields.image, 'large' ).uri }}" />

``ng_render_field`` and ``ng_image_alias`` are shown in more detail in the examples below. There are
two other Twig functions, ``ng_query`` and ``ng_raw_query``. These are used with Query Types and are
documented separately on :doc:`Query Types reference</reference/query_types>` documentation page.

Basic usage
-----------

- **Accessing Location's Content object**

  Content is available in the Location's property ``content``:

  .. code-block:: twig

    {{ set content = location.content }}

- **Displaying the name of a Content**

  Content's name is available in the ``name`` property:

  .. code-block:: twig

    <h1>Content's name: {{ content.name }}</h1>

- **Linking to a Location**

  Linking is done using the ``path()`` Twig function, same as before.

  .. code-block:: twig

    <a href="{{ path(location) }}">{{ location.content.name }}</a>

- **Linking to a Content**

  Linking to Content will create a link to Content's main Location.

  .. code-block:: twig

    <a href="{{ path(content) }}">{{ content.name }}</a>

Working with Content fields
---------------------------

- **Accessing a Content Field**

  .. note::

    Content's fields are lazy-loaded, which means they will be transparently loaded only at the
    point you access them.

  The most convenient way to access a Content field in Twig is using the dot notation:

  .. code-block:: twig

    {% set title_field = content.fields.title %}

  Alternatively, you can do the same using the array notation:

  .. code-block:: twig

    {% set title_field = content.fields['title'] %}

  Or by calling ``getField()`` method on the Content object, also available as ``field()`` in Twig,
  which requires Field identifier as argument:

  .. code-block:: twig

    {% set title_field = content.field('title') %}

- **Checking if the Field exists**

  Checking if the field exists can be done with ``hasField()`` method on the Content object:

  .. code-block:: twig

    {% if content.hasField('title') %}
        <p>Content has a 'title' field</p>
    {% endif %}

- **Displaying Field's metadata**

  Field object aggregates some data from the FieldDefinition:

  .. code-block:: twig

    {% set title_field = content.fields.title %}

    <p>Field name: {{ title_field.name }}</p>
    <p>Field description: {{ title_field.description }}</p>
    <p>FieldType identifier: {{ title_field.fieldTypeIdentifier }}</p>

- **Rendering the field using the configured template**

  To render a field in vanilla eZ Platform you would use
  `ez_render_field <https://doc.ezplatform.com/en/2.2/guide/twig_functions_reference/#ez_render_field>`_ function, which
  does that using the `configured template block <https://doc.ezplatform.com/en/2.2/guide/templates/#using-the-field-types-template-block>`_.
  For the same purpose and using the same templates, Site API provides it's own function
  ``ng_render_field``. It has two parameters:

  1. **required** Field object
  2. **optional** hash of parameters, by default an empty array ``[]``

     This parameter is exactly the same as you would use with ``ez_render_field``. The only
     exception is the ``lang`` parameter, used to override the language of the rendered field, which
     is not used by the ``ng_render_field``.

  Basic usage:

  .. code-block:: twig

    {{ ng_render_field( content.fields.title ) }}

  Using the second parameter to override the default template block:

  .. code-block:: twig

    {{
        ng_render_field(
            content.fields.title,
            { 'template': 'AcmeTestBundle:fields:my_field_template.html.twig' }
        )
    }}

- **Checking if the Field's value is empty**

  This is done by calling ``isEmpty()`` method on the Field object, also available as
  ``empty()`` or just ``empty`` in Twig:

  .. code-block:: twig

    {% if content.fields.title.empty %}
        <p>Title is empty</p>
    {% else %}
        {{ ng_render_field( content.fields.title ) }}
    {% endif %}

- **Accessing the Field's value**

  Typically you would render the field using ``ng_render_field`` Twig function, but if needed you
  can also access field's value directly. Value format varies by the FieldType, so you'll need to
  know about the type of the Field whose value you're accessing. You can find out more about that on
  the official `FieldType reference page <https://doc.ezplatform.com/en/latest/api/field_type_reference/>`_
  or even looking at the value's code.

  Here we'll assume ``title`` field is of the FieldType ``ezstring``. Latest code for that
  FieldType's value can be found `here <https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/Core/FieldType/TextLine/Value.php>`_.

  .. code-block:: twig

    <h1>Value of the title field is: '{{ content.field.title.value.text }}'</h1>

- **Rendering the image field**

  Typically for this you would use the built-in template through ``ng_render_field`` function, but
  you can also do it manually if needed:

  .. code-block:: twig

    {% set image = content.fields.image %}

    {% if not image.empty %}
        <img src="{{ ng_image_alias( image, 'i1140' ).uri }}"
             alt="{{ image.value.alternativeText }}" />
    {% endif %}

Traversing the Content model
----------------------------

Content Locations
~~~~~~~~~~~~~~~~~

- **Accessing the main Location of a Content**

  .. code-block:: twig

    {% set main_location = content.mainLocation %}

- **Listing Content's Locations**

  This is done by calling the method ``getLocations()``, also available as ``locations()`` in
  Twig. It returns an array of Locations sorted by the path string (e.g. ``/1/2/191/300/``) and
  optionally accepts maximum number of items returned (by default ``25``).

  .. code-block:: twig

    {% set locations = content.locations(10) %}

    <p>First 10 Content's Locations:</p>

    <ul>
    {% for location in locations %}
        <li>
            <a href="{{ path(location) }}">Location #{{ location.id }}</a>
        </li>
    {% endif %}
    </ul>

- **Paginating through Content's Locations**

  This is done by calling the method ``filterLocations()``, which returns a ``Pagerfanta``
  instance with Locations sorted by the path string (e.g. ``/1/2/191/300/``) and accepts two
  optional parameters:

  1. **optional** maximum number of items per page, by default ``25``
  2. **optional** current page, by default ``1``

  .. code-block:: twig

    {% set locations = content.filterLocations(10, 2) %}

    <h3>Content's Location, page {{ locations.currentPage }}</h3>
    <p>Total: {{ locations.nbResults }} items</p>

    <ul>
    {% for location in locations %}
        <li>
            <a href="{{ path(location) }}">Location #{{ location.id }}</a>
        </li>
    {% endfor %}
    </ul>

    {{ pagerfanta( locations, 'twitter_bootstrap' ) }}

Content Field relations
~~~~~~~~~~~~~~~~~~~~~~~

- **Accessing a single field relation**

  This is done by calling the method ``getFieldRelation()``, also available as
  ``fieldRelation()`` in Twig. It has one required parameter, which is the identifier of the
  relation field. In our example, the relation field's identifier is ``related_article``.

  .. code-block:: twig

    {% set related_content = content.fieldRelation('related_article') %}

    {% if related_content is defined %}
        <a href="{{ path(related_content) }}">{{ related_content.name }}</a>
    {% else %}
        <p>There are two possibilities:</p>
        <ol>
            <li>Relation field 'related_article' is empty</p>
            <li>You don't have a permission to read the related Content</li>
        </ol>
        <p>In any case, you can't render the related Content!</p>
    {% endif %}

  .. note::

    If relation field contains multiple relations, the first one will be returned. If it doesn't
    contain relations or you don't have the access to read the related Content, the method will
    return ``null``. Make sure to check if that's the case.

- **Accessing all field relations**

  This is done by calling the method ``getFieldRelations()``, also available as
  ``fieldRelations()`` in Twig. It returns an array of Content items and has two parameters:

  1. **required** identifier of the relation field
  2. **optional** maximum number of items returned, by default ``25``

  .. code-block:: twig

    {% set related_articles = content.fieldRelations('related_articles', 10) %}

    <ul>
    {% for article in related_articles %}
        <a href="{{ path(article) }}">{{ article.name }}</a>
    {% endfor %}
    </ul>

- **Filtering through field relations**

  This is done by calling the method ``filterFieldRelations()``, which returns a Pagerfanta
  instance and has four parameters:

  1. **required** identifier of the relation field
  2. **optional** array of ContentType identifiers that will be used to filter the result, by
     default an empty array ``[]``
  3. **optional** maximum number of items per page, by default ``25``
  4. **optional** current page, by default ``1``

  .. code-block:: twig

    {% set articles = content.filterFieldRelations('related_items', ['article'], 10, 1) %}

    <ul>
    {% for article in articles %}
        <a href="{{ path(article) }}">{{ article.name }}</a>
    {% endfor %}
    </ul>

    {{ pagerfanta( events, 'twitter_bootstrap' ) }}

Location children
~~~~~~~~~~~~~~~~~

- **Listing Location's children**

  This is done by calling the method ``getChildren()``, also available as ``children()`` in
  Twig. It returns an array of children Locations and optionally accepts maximum number of items
  returned (by default ``25``).

  .. code-block:: twig

    {% set children = location.children(10) %}

    <h3>List of 10 Location's children, sorted as is defined on the Location</h3>

    <ul>
    {% for child in children %}
        <a href="{{ path(child) }}">{{ child.name }}</a>
    {% endfor %}
    </ul>

- **Filtering through Location's children**

  This is done by calling the method ``filterChildren()``, which returns a Pagerfanta instance
  and has three parameters:

  1. **optional** array of ContentType identifiers that will be used to filter the result, by default
     an empty array ``[]``
  2. **optional** maximum number of items per page, by default ``25``
  3. **optional** current page, by default ``1``

  .. code-block:: twig

    {% set documents = location.filterChildren(['document'], 10, 1) %}

    <h3>Children documents, page {{ documents.currentPage }}</h3>
    <p>Total: {{ documents.nbResults }} items</p>

    <ul>
    {% for document in documents %}
        <a href="{{ path(document) }}">{{ document.name }}</a>
    {% endfor %}
    </ul>

    {{ pagerfanta( documents, 'twitter_bootstrap' ) }}

Location siblings
~~~~~~~~~~~~~~~~~

- **Listing Location's siblings**

  This is done by calling the method ``getSiblings()``, also available as ``siblings()`` in
  Twig. It returns an array of children Locations and optionally accepts maximum number of items
  returned (by default ``25``).

  .. code-block:: twig

    {% set children = location.siblings(10) %}

    <h3>List of 10 Location's siblings, sorted as is defined on the parent Location</h3>

    <ul>
    {% for sibling in siblings %}
        <a href="{{ path(sibling) }}">{{ sibling.name }}</a>
    {% endfor %}
    </ul>

- **Filtering through Location's siblings**

  This is done by calling the method ``filterSiblings()``, which returns a Pagerfanta instance
  and has three parameters:

  1. **optional** array of ContentType identifiers that will be used to filter the result, by default
     an empty array ``[]``
  2. **optional** maximum number of items per page, by default ``25``
  3. **optional** current page, by default ``1``

  .. code-block:: twig

    {% set articles = location.filterSiblings(['article'], 10, 1) %}

    <h3>Sibling articles, page {{ articles.currentPage }}</h3>
    <p>Total: {{ articles.nbResults }} items</p>

    <ul>
    {% for article in articles %}
        <a href="{{ path(articles) }}">{{ articles.name }}</a>
    {% endfor %}
    </ul>

    {{ pagerfanta( articles, 'twitter_bootstrap' ) }}
