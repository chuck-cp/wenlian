{% extends 'templates/main.volt' %}

{% block content %}

    {% if top_categories|length > 1 %}
        {{ partial('exam_paper/list_filter') }}
    {% endif %}

    {% set pager_url = url({'for':'home.exam_paper.pager'}, params) %}

    <div id="paper-list" data-url="{{ pager_url }}"></div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/list.filter.js') }}
    {{ js_include('home/js/exam.paper.list.js') }}

{% endblock %}