{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.affiliate'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>分销设置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启一级分销</label>
            <div class="layui-input-block">
                <input type="radio" name="v1_com_enabled" value="1" title="是" {% if affiliate.v1_com_enabled == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="v1_com_enabled" value="0" title="否" {% if affiliate.v1_com_enabled == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开启二级分销</label>
            <div class="layui-input-block">
                <input type="radio" name="v2_com_enabled" value="1" title="是" {% if affiliate.v2_com_enabled == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="v2_com_enabled" value="0" title="否" {% if affiliate.v2_com_enabled == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开启三级分销</label>
            <div class="layui-input-block">
                <input type="radio" name="v3_com_enabled" value="1" title="是" {% if affiliate.v3_com_enabled == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="v3_com_enabled" value="0" title="否" {% if affiliate.v3_com_enabled == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">一级佣金比例</label>
            <div class="layui-input-block">
                <select name="v1_com_rate" lay-verify="required">
                    {% for value in 1..30 %}
                        {% set selected = value == affiliate.v1_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">二级佣金比例</label>
            <div class="layui-input-block">
                <select name="v2_com_rate" lay-verify="required">
                    {% for value in 1..20 %}
                        {% set selected = value == affiliate.v2_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">三级佣金比例</label>
            <div class="layui-input-block">
                <select name="v3_com_rate" lay-verify="required">
                    {% for value in 1..10 %}
                        {% set selected = value == affiliate.v3_com_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
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