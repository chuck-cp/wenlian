{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('withdraw/macro') }}

    {% set search_url = url({'for':'admin.withdraw.search'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>提现管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=export">
                <i class="layui-icon layui-icon-export"></i>导出提现
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}?target=search">
                <i class="layui-icon layui-icon-search"></i>搜索提现
            </a>
        </div>
    </div>

    <table class="layui-table kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>用户信息</th>
            <th>提现编号</th>
            <th>提现金额</th>
            <th>服务费</th>
            <th>提现状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
            {% set show_url = url({'for':'admin.withdraw.show','id':item.id}) %}
            <tr>
                <td><a href="{{ user_url }}">{{ item.user.name }}</a>（{{ item.user.id }}）</td>
                <td>{{ item.sn }}</td>
                <td>{{ '￥%0.2f'|format(item.apply_amount) }}</td>
                <td>{{ '￥%0.2f'|format(item.service_fee) }}</td>
                <td>{{ withdraw_status(item.status) }}</td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td class="center">
                    <a class="layui-btn layui-btn-sm" href="{{ show_url }}">详情</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}