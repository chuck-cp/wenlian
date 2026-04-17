{% extends 'templates/main.volt' %}

{% block content %}

    {% if pager.total_pages > 0 %}
        <table class="layui-table kg-table">
            <colgroup>
                <col width="10%">
                <col>
                <col>
                <col>
                <col width="10%">
            </colgroup>
            <thead>
            <tr>
                <th>用户头像</th>
                <th>用户名称</th>
                <th>下属级别</th>
                <th>加入时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
                <tr>
                    <td><img class="kg-avatar-sm" src="{{ item.user.avatar }}" alt="{{ item.user.name }}"></td>
                    <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a></td>
                    <td>{{ '%s级'|format(item.level) }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                    <td><a class="layui-btn layui-btn-sm" href="{{ user_url }}">查看用户</a></td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {{ partial('partials/pager_ajax') }}
    {% endif %}

{% endblock %}