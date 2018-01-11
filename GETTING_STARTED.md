# Getting started with Site API

This guide presumes that you've already installed and enabled Site API inside your project.

First you need to activate the full view route override:

```yaml
netgen_ez_platform_site_api:
    system:
        frontend_group:
            override_url_alias_view_action: true
```

Then you define the override rule for the template:

```yaml
ezpublish:
    system:
        frontend_group:
            ngcontent_view:
                full:
                    article:
                        template: "@App/content/full/article.html.twig"
                        controller: "AppBundle:Demo:viewArticle"
                        match:
                            Identifier\ContentType: article
```

The controller:

```php
<?php

namespace AppBundle\Controller;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\Controller;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;

class DemoController extends Controller
{
    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function viewArticleAction(ContentView $view)
    {
        $content = $view->getSiteContent();
        $location = $view->getSiteLocation();
    
        $filterService = $this-getSite()->getFilterService();
    
        $hasRelatedItems = false;
        // No need to use FieldHelper here
        if (!$content->getField('related')->isEmpty()) {
            $hasRelatedItems = true;
        }
    
        // Custom logic here
        ...
    
        $view->addParameters(
            array(
                // Adding a variable to the view
                'has_related' => $hasRelatedItems,
            )
        );
    
        return $view;
    }
}
```


Finally, create the template:

```twig
{# @App/content/full/article.html.twig #}

{% extends '@App/pagelayout.html.twig' %}

{% block content %}
    {# In here, you have access to content and location Site API objects #}
{% endblock %}
```

Registering controller as service:

```yaml
app.controller.demo:
    parent: netgen.ezplatform_site.controller.base
    class: AppBundle\Controller\DemoController
```

You need to update override rule accordingly:

```yaml
ezpublish:
    system:
        frontend_group:
            ngcontent_view:
                full:
                    article:
                        template: "@App/content/full/article.html.twig"
                        controller: "app.controller.demo:viewArticleAction"
                        match:
                            Identifier\ContentType: article
```

### Using Site API inside Twig templates

#### Generic functions

Reading value from Content field:

```twig
<h1>Article title: {{ content.fields.title.value }}</h1>
```

Rendering field value:

```twig
<h1>{{ ng_render_field(content.field.title) }}</h1>
```

Checking if field is not empty:
```twig
{% if not content.fields.title.empty %}
	{{ ng_render_field(content.fields.title }}
{% endif %}
```

Getting image alias:
```twig
{% set image = content.fields.image %}
{% if not image.empty %}
     <img src="{{ ng_image_alias( image, 'i1140' ).uri|default('') }}" alt="{{ image.value.alternativeText }}" />
{% endif %}
```

Getting field:
```twig
{% set title_field_value = content.fields.title %}
```

Displaying content name:
```twig
<h1>Article name: {{ content.name }}</h1>
```

#### Content related

Displaying main location of current content:
```twig
<h1>Main location content name: {{ content.mainLocation.content.name }}
```

Displaying owner:
```twig
<p>Article owner: {{ content.owner.name }}</p>
```

Displaying all locations of current content (default limit is 25):
```twig
<ul>
{% for location in content.locations %}
	<li>Location name: {{ location.content.name }}</li>
{% endfor %}
</ul>
```

Displaying all locations of current content by simple criteria (limit and offset):
```twig
{% set locations = content.filterLocations(15, 2) %}
<ul>
{% for location in locations %}
	<li>Location name: {{ location.content.name }}</li>
{% endfor %}
</ul>
```

Display related content (object relation field):
```twig
{% if not content.fields.related_article.empty %}
	{% set related_article = content.getFieldRelation('related_article') %}
	# field is not empty
	# but you maybe do not have permissions to read
	{% if related_article is not empty %}
		<p>Related article name: {{ related_article.name }}</p>
	{% endif %}
{% endif %} 
```

Display related contents (object relations field):
```twig
{% if not content.fields.related_articles.empty %}
	# default limit is 25
	{% set related_articles = content.getFieldRelations('related_articles') %}
	# field is not empty
	# but you maybe do not have permissions to read
	{% if related_articles is not empty %}
		{% for related_article in related_articles %}
			<p>Related article name: {{ related_article.name }}</p>
		{% endfor %}
	{% endif %}
{% endif %} 
```

Display related contents by simple criteria (object relations field):
```twig
{% if not content.fields.related.empty %}
	# default limit is 25
	{% set related_blog_posts = content.filterFieldRelations('related', ['blog_post'], 10, 4) %}
	# field is not empty
	# but you maybe do not have permissions to read
	{% if related_blog_posts is not empty %}
		{% for related_blog_post in related_blog_posts %}
			<p>Related blog post name: {{ related_blog_post.name }}</p>
		{% endfor %}
	{% endif %}
{% endif %} 
```

#### Location related

Displaying parent location of current location:
```twig
<h1>Parent location content name: {{ content.mainLocation.parent.content.name }}
```

Displaying all children of current location (default limit is 25):
```twig
<ul>
{% for child in content.location.children %}
	<li>Child name: {{ child.content.name }}</li>
{% endfor %}
</ul>
```

Displaying children of current location by simple criteria (content type, limit and offset):
```twig
{% set children = content.location.filterChildren(['blog_post'], 10, 2) %}
<ul>
{% for child in children %}
	<li>Child name: {{ child.content.name }}</li>
{% endfor %}
</ul>
```

Displaying all siblings of current location (default limit is 25):
```twig
<ul>
{% for sibling in content.location.siblings %}
	<li>Sibling name: {{ sibling.content.name }}</li>
{% endfor %}
</ul>
```

Displaying siblings of current location by simple criteria (content type, limit and offset):
```twig
{% set children = content.location.filterSiblings(['blog_post'], 10, 2) %}
<ul>
{% for sibling in siblings %}
	<li>Sibling name: {{ sibling.content.name }}</li>
{% endfor %}
</ul>
```
