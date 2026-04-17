{% extends 'templates/main.volt' %}

{% block content %}

    {% set target = request.get('target','string','search') %}
    {% set count = request.get('count','int',-1) %}

    {% if target == 'search' %}
        {% set action_url = url({'for':'admin.exam_paper.learnings','id':paper.id}) %}
        {% set title = '搜索记录' %}
    {% else %}
        {% set action_url = url({'for':'admin.exam_paper.export_learning','id':paper.id}) %}
        {% set title = '导出记录' %}
    {% endif %}

    <form class="layui-form kg-form" method="GET" action="{{ action_url }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>{{ title }}</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">所属试卷</label>
            <div class="layui-input-block">
                <div class="layui-form-mid">{{ paper.title }}</div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">用户账号</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="user_id" placeholder="用户编号 / 手机号码 / 邮箱地址">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">加入方式</label>
            <div class="layui-input-block">
                {% for value,title in source_types %}
                    <input type="checkbox" name="source_type[]" value="{{ value }}" title="{{ title }}">
                {% endfor %}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">首次考试</label>
            <div class="layui-input-block">
                <input type="radio" name="debut" value="1" title="是">
                <input type="radio" name="debut" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">考试时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="create_time[]" autocomplete="off">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="create_time[]" autocomplete="off">
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