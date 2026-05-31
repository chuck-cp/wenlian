{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/certificate') }}

    {% set add_url = url({'for':'admin.cert.add'}) %}
    {% set search_url = url({'for':'admin.cert.search'}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a><cite>证书管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加证书
            </a>
            <a class="layui-btn layui-btn-sm" href="{{ search_url }}">
                <i class="layui-icon layui-icon-search"></i>搜索证书
            </a>
        </div>
    </div>

    <table class="kg-table layui-table layui-form">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>证书名称</th>
            <th>关联内容</th>
            <th>授予数量</th>
            <th>发布</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set users_url = url({'for':'admin.cert.users','id':item.id}) %}
            {% set grant_url = url({'for':'admin.cert.grant','id':item.id}) %}
            {% set edit_url = url({'for':'admin.cert.edit','id':item.id}) %}
            {% set update_url = url({'for':'admin.cert.update','id':item.id}) %}
            {% set delete_url = url({'for':'admin.cert.delete','id':item.id}) %}
            {% set restore_url = url({'for':'admin.cert.restore','id':item.id}) %}
            <tr>
                <td><a href="{{ edit_url }}">{{ item.name }}</a>（{{ item.id }}）</td>
                <td>{{ item_info(item.item_type,item.item_info) }}</td>
                <td>{{ item.grant_count }}</td>
                <td><input type="checkbox" name="published" value="1" lay-text="是|否" lay-skin="switch" lay-filter="go" data-url="{{ update_url }}"
                           {% if item.published == 1 %}checked="checked"{% endif %}></td>
                <td class="center">
                    <div class="kg-dropdown">
                        <button class="layui-btn layui-btn-sm">操作 <i class="layui-icon layui-icon-triangle-d"></i></button>
                        <ul>
                            <li><a href="{{ edit_url }}">编辑证书</a></li>
                            {% if item.deleted == 0 and item.grant_type == 2 %}
                                <li><a href="{{ grant_url }}">手动发放</a></li>
                            {% endif %}
                            {% if item.deleted == 0 %}
                                <li><a href="javascript:" class="kg-delete" data-url="{{ delete_url }}">删除证书</a></li>
                            {% else %}
                                <li><a href="javascript:" class="kg-restore" data-url="{{ restore_url }}">还原证书</a></li>
                            {% endif %}
                            <li><a href="{{ users_url }}">授予记录</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    {{ partial('partials/pager') }}

{% endblock %}
