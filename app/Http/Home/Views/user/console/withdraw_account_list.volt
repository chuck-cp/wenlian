{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/withdraw_account') }}

    {% set add_url = url({'for':'home.uc.withdraw'},{'action':'account.add'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">提现账户</span>
                    <a class="layui-btn layui-btn-sm sub-nav" href="{{ add_url }}">添加账户</a>
                </div>
                {% if items|length > 0 %}
                    <table class="layui-table" lay-size="lg" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col width="15%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>姓名</th>
                            <th>提现平台</th>
                            <th>平台帐号</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in items %}
                            {% set delete_url = url({'for':'home.withdraw_account.delete','id':item.id}) %}
                            <tr>
                                <td>{{ item.name }}</td>
                                <td>{{ channel_type(item.channel) }}</td>
                                <td>{{ item.account }}</td>
                                <td>
                                    <button class="layui-btn layui-btn-sm layui-btn-danger kg-delete" data-url="{{ delete_url }}">删除</button>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}