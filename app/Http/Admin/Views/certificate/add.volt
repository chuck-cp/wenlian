{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.cert.create'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>添加证书</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">证书名称</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="name" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">证书类型</label>
            <div class="layui-input-block">
                {% for value,title in item_types %}
                    {% set checked = value == 1 ? 'checked="checked"' : '' %}
                    <input type="radio" name="item_type" value="{{ value }}" title="{{ title }}" {{ checked }} lay-filter="item_type">
                {% endfor %}
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