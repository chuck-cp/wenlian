{% extends 'templates/main.volt' %}

{% block content %}

    {% set update_url = url({'for':'admin.point_gift.update','id':gift.id}) %}

    {% if gift.type == 1 %}
        {{ partial('point_gift/edit_course') }}
    {% elseif gift.type == 3 %}
        {{ partial('point_gift/edit_vip') }}
    {% elseif gift.type == 4 %}
        {{ partial('point_gift/edit_exam_paper') }}
    {% elseif gift.type == 5 %}
        {{ partial('point_gift/edit_article') }}
    {% elseif gift.type == 100 %}
        {{ partial('point_gift/edit_goods') }}
    {% endif %}

{% endblock %}

{% block link_css %}

{% endblock %}

{% block include_js %}

    {% if gift.type == 100 %}
        {{ js_include('lib/kindeditor/kindeditor.min.js') }}
        {{ js_include('admin/js/content.editor.js') }}
        {{ js_include('admin/js/cover.upload.js') }}
    {% endif %}

{% endblock %}