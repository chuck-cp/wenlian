{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/groupon_team_user') }}

    <table class="layui-table kg-table">
        <colgroup>
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>用户信息</th>
            <th>加入时间</th>
            <th>支付状态</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
            <tr>
                <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a>（{{ item.user.id }}）</td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td>{{ pay_status(item.status) }}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
