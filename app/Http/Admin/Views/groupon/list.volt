{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/sale') }}
    {{ partial('macros/groupon') }}

    {% set add_url = url({'for':'admin.groupon.add'}) %}
    {% set search_url = url({'for':'admin.groupon.search'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>拼团管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加商品
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索商品
            </a>
        </div>
    </div>

    <table class="kg-table layui-table layui-form">
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
            <th>商品信息</th>
            <th>拼团价格</th>
            <th>开团数 / 成团数</th>
            <th>活动时间</th>
            <th>活动状态</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set groupon_url = url({'for':'home.groupon.show','id':item.id}) %}
            {% set edit_url = url({'for':'admin.groupon.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.groupon.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.groupon.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.groupon.restore','id':item.id}) %}
            {% set teams_url = url({'for':'admin.groupon.teams','id':item.id}) %}
            <tr>
                <td>{{ item_full_info(item.item_type,item.item_info) }}</td>
                <td>
                    <p>成员：{{ '￥%0.2f'|format(item.member_price) }}</p>
                    <p>团长：{{ '￥%0.2f'|format(item.leader_price) }}</p>
                </td>
                <td>{{ item.total_team_count }} / {{ item.finish_team_count }}</td>
                <td>
                    <p>开始：{{ date('Y-m-d H:i:s',item.start_time) }}</p>
                    <p>结束：{{ date('Y-m-d H:i:s',item.end_time) }}</p>
                </td>
                <td>{{ status_info(item.start_time,item.end_time) }}</td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}></td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            {% if item.published == 1 %}
                                <li><a href="{{ groupon_url }}" target="_blank">查看拼团</a></li>
                            {% endif %}
                            <li><a href="{{ edit_url }}">编辑拼团</a></li>
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除拼团</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原拼团</a></li>
                            {% endif %}
                            <hr>
                            <li><a href="{{ teams_url }}">拼团队伍</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
