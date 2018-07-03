# -*- coding: utf-8 -*-

import sys, os
import sphinx_rtd_theme

from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer
from pygments.lexers.web import HtmlLexer
from pygments.lexers.web import JsonLexer

from datetime import datetime

lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)
lexers['html'] = HtmlLexer(startinline=True)
lexers['json'] = JsonLexer(startinline=True)

extensions = ['sphinx.ext.autodoc',
    'sphinx.ext.doctest',
    'sphinx.ext.todo',
    'sphinx.ext.coverage',
    'sphinx.ext.imgmath',
    'sphinx.ext.ifconfig',
    'sensio.sphinx.configurationblock']

source_suffix = '.rst'
master_doc = 'index'

project = 'Netgen Layouts'
copyright = 'Netgen'
author = 'Netgen'

version = ''
release = ''

exclude_patterns = ['_build']

html_theme = "sphinx_rtd_theme"
html_theme_path = [sphinx_rtd_theme.get_html_theme_path()]

html_static_path = ['_static']
templates_path = ['_templates']

html_theme_options = {
    'collapse_navigation': True,
    'display_version': True,
}

html_context = {
    'copyright_url': 'https://www.netgenlabs.com',
    'current_year': datetime.utcnow().year
}

def setup(app):
    app.add_stylesheet("css/style.css")
