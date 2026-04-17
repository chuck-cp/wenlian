{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/course') }}

    <div class="layui-breadcrumb breadcrumb">
        <a href="/">首页</a>
        <a><cite>套餐</cite></a>
        <a><cite>{{ package.title }}</cite></a>
    </div>

    <div class="course-list">
        <div class="layui-row layui-col-space20">
            {% for course in courses %}
                <div class="layui-col-md3">
                    {{ course_card(course) }}
                </div>
            {% endfor %}
        </div>
    </div>

{% endblock %}