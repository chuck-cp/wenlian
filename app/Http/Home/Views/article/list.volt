{% extends 'templates/main.volt' %}

{% block content %}

    {% if top_categories|length > 1 %}
        {{ partial('article/list_filter') }}
    {% endif %}

    {% set pager_url = url({'for':'home.article.pager'}, params) %}

    <div id="article-list" data-url="{{ pager_url }}"></div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/list.filter.js') }}
    {{ js_include('home/js/article.list.js') }}

{% endblock %}