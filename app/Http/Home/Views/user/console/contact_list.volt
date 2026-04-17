{% extends 'templates/main.volt' %}

{% block content %}

    {% set add_url = url({'for':'home.uc.contact'},{'action':'add'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">收货地址</span>
                    <a class="layui-btn layui-btn-sm sub-nav" href="{{ add_url }}">添加地址</a>
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
                            <th>地址</th>
                            <th>联系人</th>
                            <th>手机号</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in items %}
                            {% set delete_url = url({'for':'home.user_contact.delete','id':item.id}) %}
                            {% set address = [item.add_province,item.add_city,item.add_county,item.add_other] %}
                            <tr>
                                <td>{{ address|join(' / ') }}</td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.phone }}</td>
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