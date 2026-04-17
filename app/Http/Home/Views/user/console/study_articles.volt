{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/ownership') }}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu_study') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">在学专栏</span>
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
                            <th>文章</th>
                            <th>浏览</th>
                            <th>点赞</th>
                            <th>评论</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set show_url = url({'for':'home.article.show','id':item.article.id}) %}
                            <tr>
                                <td>
                                    <p>标题：<a href="{{ show_url }}" target="_blank">{{ item.article.title }}</a></p>
                                    <p class="meta">
                                        来源：<span class="layui-badge layui-bg-gray">{{ join_source_type(item.source_type) }}</span>
                                        {% if item.expiry_time > 0 %}
                                            期限：<span class="layui-badge layui-bg-gray">{{ date('Y-m-d',item.expiry_time) }}</span>
                                        {% endif %}
                                    </p>
                                </td>
                                <td>{{ item.article.view_count }}</td>
                                <td>{{ item.article.like_count }}</td>
                                <td>{{ item.article.comment_count }}</td>
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
