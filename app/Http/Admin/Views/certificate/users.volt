{% extends 'templates/main.volt' %}

{% block content %}

    {% set search_url = url({'for':'admin.cert.search_user','id':cert.id}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a class="kg-back"><i class="layui-icon layui-icon-return"></i>返回</a>
                <a><cite>{{ cert.name }}</cite></a>
                <a><cite>授予记录</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索用户
            </a>
        </div>
    </div>

    <table class="layui-table kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>用户头像</th>
            <th>用户信息</th>
            <th>证书编号</th>
            <th>授予时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
            {% set delete_url = url({'for':'admin.cert.delete_user','id':item.id}) %}
            <tr>
                <td><img class="kg-avatar-sm" src="{{ item.user.avatar }}" alt="{{ item.user.name }}"></td>
                <td><a href="{{ user_url }}">{{ item.user.name }}</a>（{{ item.user.id }}）</td>
                <td>{{ item.sn }}</td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td class="kg-center">
                    <a class="layui-btn layui-btn-sm layui-btn-danger kg-delete" href="javascript:"
                       data-tips="确定要撤销吗？"
                       data-url="{{ delete_url }}">撤销</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
