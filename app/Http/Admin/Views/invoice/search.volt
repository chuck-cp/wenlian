{% extends 'templates/main.volt' %}

{% block content %}

    {% set target = request.get('target','string','search') %}
    {% set count = request.get('count','int',-1) %}

    {% if target == 'search' %}
        {% set action_url = url({'for':'admin.invoice.list'}) %}
        {% set title = '搜索开票' %}
    {% else %}
        {% set action_url = url({'for':'admin.invoice.export'}) %}
        {% set title = '导出开票' %}
    {% endif %}

    <form class="layui-form kg-form" method="GET" action="{{ action_url }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>{{ title }}</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开票编号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="id" placeholder="开票编号精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">用户帐号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="user_id" placeholder="用户编号 / 手机号码 / 邮箱地址 精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发票抬头</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="head_name" placeholder="发票抬头精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发票代码</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="sort_no" placeholder="发票代码精确匹配">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发票号码</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="serial_no" placeholder="发票号码精确匹配">
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">创建时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="create_time[]" autocomplete="off">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="create_time[]" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发票类型</label>
            <div class="layui-input-block">
                {% for value,title in usage_types %}
                    <input type="checkbox" name="usage_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">抬头类型</label>
            <div class="layui-input-block">
                {% for value,title in head_types %}
                    <input type="checkbox" name="head_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开票状态</label>
            <div class="layui-input-block">
                {% for value,title in status_types %}
                    <input type="checkbox" name="status[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true" lay-filter="search">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
                <input type="hidden" name="target" value="{{ target }}">
                <input type="hidden" name="count" value="{{ count }}">
            </div>
        </div>
    </form>

{% endblock %}

{% block include_js %}

    {{ js_include('admin/js/export.search.js') }}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time', '#end-time'],
            });

        });

    </script>

{% endblock %}