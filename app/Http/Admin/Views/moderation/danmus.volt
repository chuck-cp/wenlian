{% extends 'templates/main.volt' %}

{% block content %}

    {% set batch_moderate_url = url({'for':'admin.danmu.batch_moderate'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>弹幕审核</cite></a>
            </span>
            <span class="layui-btn layui-btn-sm layui-bg-green kg-batch" data-url="{{ batch_moderate_url }}?type=approve">批量通过</span>
            <span class="layui-btn layui-btn-sm layui-bg-red kg-batch" data-url="{{ batch_moderate_url }}?type=reject">批量拒绝</span>
        </div>
    </div>

    <table class="layui-table layui-form kg-table">
        <colgroup>
            <col width="5%">
            <col>
            <col>
            <col>
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th><input class="all" type="checkbox" lay-filter="all"></th>
            <th>作者信息</th>
            <th>弹幕内容</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set owner_url = url({'for':'home.user.show','id':item.owner.id}) %}
            {% set moderate_url = url({'for':'admin.danmu.moderate','id':item.id}) %}
            <tr>
                <td><input class="item" type="checkbox" value="{{ item.id }}" lay-filter="item"></td>
                <td><a href="{{ owner_url }}" target="_blank">{{ item.owner.name }}</a>（{{ item.owner.id }}）</td>
                <td><p class="layui-elip kg-item-elip" title="{{ item.text }}">{{ item.text }}</p></td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td class="center">
                    <a href="{{ moderate_url }}" class="layui-btn layui-btn-sm">详情</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}