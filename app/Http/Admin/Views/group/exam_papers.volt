{% extends 'templates/main.volt' %}

{% block content %}

    {% set back_url = url({'for':'admin.group.list'}) %}
    {% set add_url = url({'for':'admin.group.add_exam_paper','id':group.id}) %}

    <div class="kg-nav">
        <div class="kg-nav-left">
            <span class="layui-breadcrumb">
                <a href="{{ back_url }}"><i class="layui-icon layui-icon-return"></i>返回</a>
                <a><cite>{{ group.name }}</cite></a>
                <a><cite>试卷管理</cite></a>
            </span>
        </div>
        <div class="kg-nav-right">
            <a class="layui-btn layui-btn-sm" href="{{ add_url }}">
                <i class="layui-icon layui-icon-add-1"></i>添加试卷
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
            <th>编号</th>
            <th>标题</th>
            <th>价格</th>
            <th>加入时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set paper_url = url({'for':'home.exam_paper.show','id':item.paper.id}) %}
            {% set delete_url = url({'for':'admin.group.delete_exam_paper','id':item.id}) %}
            <tr>
                <td>{{ item.paper.id }}</td>
                <td><a href="{{ paper_url }}" target="_blank">{{ item.paper.title }}</a></td>
                <td>{{ '￥%0.2f'|format(item.paper.market_price) }}</td>
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
