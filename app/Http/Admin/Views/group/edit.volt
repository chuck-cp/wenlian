{% extends 'templates/main.volt' %}

{% block content %}

    {% set expiry_time = group.expiry_time > 0 ? date('Y-m-d H:i:s',group.expiry_time) : '' %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.group.update','id':group.id}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>编辑分组</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="name" value="{{ group.name }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">过期时间</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="expiry_time" value="{{ expiry_time }}" autocomplete="off" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发布</label>
            <div class="layui-input-block">
                <input type="radio" name="published" value="1" title="是" {% if group.published == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="published" value="0" title="否" {% if group.published == 0 %}checked="checked"{% endif %}>
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
                elem: 'input[name=expiry_time]',
                type: 'datetime'
            });

        });

    </script>

{% endblock %}
