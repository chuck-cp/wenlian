{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/groupon_team') }}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a class="kg-back"><i class="layui-icon layui-icon-return"></i>返回</a>
                <a><cite>拼团队伍</cite></a>
            </span>
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
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>团长信息</th>
            <th>目标订单数</th>
            <th>完成订单数</th>
            <th>创建时间</th>
            <th>过期时间</th>
            <th>队伍状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set leader_url = url({'for':'home.user.show','id':item.leader.id}) %}
            {% set team_users_url = url({'for':'admin.groupon.team_users','id':item.id}) %}
            {% set close_team_url = url({'for':'admin.groupon.close_team','id':item.id}) %}
            {% set refund_team_url = url({'for':'admin.groupon.refund_team','id':item.id}) %}
            <tr>
                <td><a href="{{ leader_url }}" target="_blank">{{ item.leader.name }}</a>（{{ item.leader.id }}）</td>
                <td>{{ item.target_order_count }}</td>
                <td>{{ item.finish_order_count }}</td>
                <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                <td>{{ date('Y-m-d H:i:s',item.expire_time) }}</td>
                <td>{{ team_status(item.status) }}</td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="javascript:" class="team-users" data-url="{{ team_users_url }}">队员列表</a></li>
                            {% if item.status == 1 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ close_team_url }}">关闭队伍</a></li>
                            {% endif %}
                            {% if item.status == 2 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ close_team_url }}">全员退款</a></li>
                            {% endif %}
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.define(['jquery', 'layer'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;

            $('.team-users').on('click', function () {
                var url = $(this).data('url');
                layer.open({
                    type: 2,
                    title: '拼团用户',
                    area: ['800px', '600px'],
                    content: url,
                });
            });

        });

    </script>

{% endblock %}
