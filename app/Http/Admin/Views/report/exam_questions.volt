{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_question') }}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>试题挑错</cite></a>
            </span>
        </div>
    </div>

    <table class="layui-table layui-form kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>试题编号</th>
            <th>试题题干</th>
            <th>挑错人次</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set edit_url = url({'for':'admin.exam_question.edit','id':item.id}) %}
            {% set report_url = url({'for':'admin.exam_question.report','id':item.id}) %}
            <tr>
                <td>{{ item.id }}</td>
                <td><a href="{{ edit_url }}">{{ item.topic }}</a></td>
                <td>{{ item.report_count }}</td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td class="center">
                    <a href="{{ report_url }}" class="layui-btn layui-btn-sm">详情</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}