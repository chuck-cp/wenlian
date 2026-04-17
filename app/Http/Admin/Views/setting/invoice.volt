{% extends 'templates/main.volt' %}

{% block content %}

    {% set usage_types = invoice.usage_types|json_decode(true) %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.invoice'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>开票设置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启服务</label>
            <div class="layui-input-block">
                <input type="radio" name="enabled" value="1" title="是" {% if invoice.enabled == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="enabled" value="0" title="否" {% if invoice.enabled == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">发票类型</label>
            <div class="layui-input-block">
                <input type="checkbox" name="usage_types[]" value="normal" title="增值税普票" {% if 'normal' in usage_types %}checked="checked"{% endif %}>
                <input type="checkbox" name="usage_types[]" value="special" title="增值税专票" {% if 'special' in usage_types %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">开票次数</label>
            <div class="layui-input-block">
                <select name="monthly_limit" lay-verify="required">
                    {% for value in 1..10 %}
                        {% set selected = value == invoice.monthly_limit ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }} 次 / 月</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小金额（元）</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="min_amount" value="{{ invoice.min_amount }}" lay-verify="number">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最大金额（元）</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="max_amount" value="{{ invoice.max_amount }}" lay-verify="number">
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