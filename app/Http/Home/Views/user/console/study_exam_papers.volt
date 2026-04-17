{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/exam_paper_user') }}
    {{ partial('macros/exam_paper') }}
    {{ partial('macros/ownership') }}

    {% set mistake_url = url({'for':'home.exam.mistake_explore'}) %}
    {% set favorite_url = url({'for':'home.exam.favorite_explore'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu_study') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">在学试卷</span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>试卷</th>
                            <th>时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set paper_url = url({'for':'home.exam_paper.show','id':item.exam_paper.id}) %}
                            <tr>
                                <td>
                                    <p>名称：<a href="{{ paper_url }}" target="_blank">{{ item.exam_paper.title }}</a></p>
                                    <p class="meta">
                                        来源：<span class="layui-badge layui-bg-gray">{{ join_source_type(item.source_type) }}</span>
                                        类型：<span class="layui-badge layui-bg-gray">{{ exam_type(item.exam_paper.exam_type) }}</span>
                                        {% if item.expiry_time > 0 %}
                                            期限：<span class="layui-badge layui-bg-gray">{{ date('Y-m-d',item.expiry_time) }}</span>
                                        {% endif %}
                                    </p>
                                </td>
                                <td>{{ item.create_time|time_ago }}</td>
                                <td>{{ exam_status(item.status) }}</td>
                                <td><a class="layui-btn layui-btn-sm" href="{{ paper_url }}">查看</a></td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {{ partial('partials/pager') }}
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}
