Templating
==========

eZ Platform Twig functions and filters comparison
-------------------------------------------------

List of eZ Platform `Twig functions`_.

.. _Twig functions: https://doc.ezplatform.com/en/2.2/guide/twig_functions_reference/


+----------------------------------------+---------------------------------------------+-----------------+
| eZ Platform                            | Netgen Site API                             | Description     |
+========================================+=============================================+=================+
| ez_content_name                        | content.name                                | displays a      |
|                                        |                                             | Content item’s  |
|                                        |                                             | name in the     |
|                                        |                                             | current         |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_field_desciption                    | content.fields.field_identifier.description | returns the     |
|                                        |                                             | description     |
|                                        |                                             | from the        |
|                                        |                                             | FieldDefinition |
|                                        |                                             | of a Content    |
|                                        |                                             | item’s Field in |
|                                        |                                             | the current     |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_field_name                          | content.fields.field_identifier.name        | returns the     |
|                                        |                                             | name from the   |
|                                        |                                             | FieldDefinition |
|                                        |                                             | of a Content    |
|                                        |                                             | item’s Field in |
|                                        |                                             | the current     |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_field_value                         | content.fields.field_identifier.value       | returns a       |
|                                        |                                             | Content item’s  |
|                                        |                                             | Field value in  |
|                                        |                                             | the current     |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_field                               | content.fields.field_identifier             | returns a Field |
|                                        |                                             | from a Content  |
|                                        |                                             | item in the     |
|                                        |                                             | current         |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_file_size                           | -                                           | returns the     |
|                                        |                                             | size of a file  |
|                                        |                                             | as string       |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_first_filled_image_field_identifier | -                                           | returns the     |
|                                        |                                             | identifier of   |
|                                        |                                             | the first image |
|                                        |                                             | field that is   |
|                                        |                                             | not empty       |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_image_alias                         | ng_image_alias                              | displays a      |
|                                        |                                             | selected        |
|                                        |                                             | variation of an |
|                                        |                                             | image           |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_is_field_empty                      | content.fields.field_identifier.empty       | checks if a     |
|                                        |                                             | Content item’s  |
|                                        |                                             | Field value is  |
|                                        |                                             | considered      |
|                                        |                                             | empty in the    |
|                                        |                                             | current         |
|                                        |                                             | language        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_render_field                        | ng_render_field                             | displays a      |
|                                        |                                             | Content item’s  |
|                                        |                                             | Field value,    |
|                                        |                                             | taking          |
|                                        |                                             | advantage of    |
|                                        |                                             | the template    |
|                                        |                                             | block exposed   |
|                                        |                                             | by the Field    |
|                                        |                                             | Type used       |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_trans_prop                          | -                                           | gets the        |
|                                        |                                             | translated      |
|                                        |                                             | value of a      |
|                                        |                                             | multi           |
|                                        |                                             | valued(translat |
|                                        |                                             | ions)           |
|                                        |                                             | property        |
+----------------------------------------+---------------------------------------------+-----------------+
| ez_urlalias                            | -                                           | is a special    |
|                                        |                                             | route name for  |
|                                        |                                             | generating URLs |
|                                        |                                             | for a Location  |
|                                        |                                             | from the given  |
|                                        |                                             | parameters      |
+----------------------------------------+---------------------------------------------+-----------------+
| -                                      | ng_query                                    | returns         |
|                                        |                                             | Pagefanta       |
|                                        |                                             | instance for    |
|                                        |                                             | configured      |
|                                        |                                             | QueryType       |
+----------------------------------------+---------------------------------------------+-----------------+
