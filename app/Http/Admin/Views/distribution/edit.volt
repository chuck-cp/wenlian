{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/distribution') }}

    {% set distribution.item_info = objectify(distribution.item_info) %}
    {% set distribution.start_time = distribution.start_time > 0 ? date('Y-m-d H:i:s',distribution.start_time) : '' %}
    {% set distribution.end_time = distribution.end_time > 0 ? date('Y-m-d H:i:s',distribution.end_time) : '' %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.distribution.update','id':distribution.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>编辑商品</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">商品信息</label>
            <div class="layui-input-block gray">{{ item_full_info(distribution.item_type,distribution.item_info) }}</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">一级佣金比例</label>
            <div class="layui-input-inline">
                <select name="v1_com_rate" lay-filter="required">
                    <option value="">请选择</option>
                    {% for value in 1..30 %}
                        {% set selected = value == distribution.v1_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">二级佣金比例</label>
            <div class="layui-input-inline">
                <select name="v2_com_rate" lay-filter="required">
                    <option value="">请选择</option>
                    {% for value in 1..20 %}
                        {% set selected = value == distribution.v2_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">三级佣金比例</label>
            <div class="layui-input-inline">
                <select name="v3_com_rate" lay-filter="required">
                    <option value="">请选择</option>
                    {% for value in 1..10 %}
                        {% set selected = value == distribution.v3_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="time-range">
            <label class="layui-form-label">活动时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="start-time" type="text" name="start_time" autocomplete="off" value="{{ distribution.start_time }}" lay-verify="required">
            </div>
            <div class="layui-form-mid gray">至</div>
            <div class="layui-input-inline">
                <input class="layui-input" id="end-time" type="text" name="end_time" autocomplete="off" value="{{ distribution.end_time }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是" {% if distribution.published == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="published" value="0" title="否" {% if distribution.published == 0 %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                <button type="button" class="kg-back layui-btn layui-btn-primary">返回</button>
            </div>
        </div>
    </form>

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['laydate'], function () {

            var laydate = layui.laydate;

            laydate.render({
                elem: '#time-range',
                type: 'datetime',
                range: ['#start-time','#end-time'],
            });

        });

    </script>

{% endblock %}
