{% extends 'templates/main.volt' %}

{% block content %}

    {% set back_url = url({'for':'admin.group.list'}) %}
    {% set add_url = url({'for':'admin.group.add_user','id':group.id}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a href="{{ back_url }}"><i class="layui-icon layui-icon-return"></i>返回</a>
                <a><cite>{{ group.name }}</cite></a>
                <a><cite>学员管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加学员
            </a>
        </div>
    </div>

    <table class="layui-table kg-table">
        <colgroup>
            <col>
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>编号</th>
            <th>昵称</th>
            <th>加入时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
            {% set delete_url = url({'for':'admin.group.delete_user','id':item.id}) %}
            <tr>
                <td>{{ item.user.id }}</td>
                <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a></td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td class="kg-center">
                    <a class="layui-btn layui-btn-danger layui-btn-sm kg-delete" href="javascript:" data-url="{{ delete_url }}">删除</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
