{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/invoice_account') }}

    {% set add_url = url({'for':'home.uc.invoice'},{'action':'account.add'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">发票抬头</span>
                    <a class="layui-btn layui-btn-sm sub-nav" href="{{ add_url }}">添加抬头</a>
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
                            <th>抬头名称</th>
                            <th>抬头类型</th>
                            <th>发票类型</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in items %}
                            {% set delete_url = url({'for':'home.invoice_account.delete','id':item.id}) %}
                            <tr>
                                <td><a href="javascript:" title="{{ account_summary_tips(item) }}">{{ item.head_name }}</a></td>
                                <td>{{ head_type(item.head_type) }}</td>
                                <td>{{ usage_type(item.usage_type) }}</td>
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