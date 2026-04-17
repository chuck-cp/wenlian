{% extends 'templates/main.volt' %}

{% block content %}

    {% set types = {'course':'课程','exam_paper':'试卷','article':'专栏','question':'问题'} %}
    {% set type = request.get('type','trim','course') %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu_study') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">我的收藏</span>
                    {% for key,value in types %}
                        {% set class = (type == key) ? 'layui-btn layui-btn-xs' : 'none' %}
                        {% set url = url({'for':'home.uc.favorites'},{'type':key}) %}
                        <a class="{{ class }}" href="{{ url }}">{{ value }}</a>
                    {% endfor %}
                </div>
                {% if type == 'course' %}
                    {{ partial('user/console/favorites_course') }}
                {% elseif type == 'article' %}
                    {{ partial('user/console/favorites_article') }}
                {% elseif type == 'question' %}
                    {{ partial('user/console/favorites_question') }}
                {% elseif type == 'exam_paper' %}
                    {{ partial('user/console/favorites_exam_paper') }}
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}