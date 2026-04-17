{% extends 'templates/main.volt' %}

{% block content %}

    {% set channels = withdraw.channels|json_decode(true) %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.withdraw'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>提现设置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">开启提现</label>
            <div class="layui-input-block">
                <input type="radio" name="enabled" value="1" title="是" {% if withdraw.enabled == "1" %}checked="checked"{% endif %}>
                <input type="radio" name="enabled" value="0" title="否" {% if withdraw.enabled == "0" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">审核方式</label>
            <div class="layui-input-block">
                <input type="radio" name="review_type" value="auto" title="自动审核" {% if withdraw.review_type == "auto" %}checked="checked"{% endif %}>
                <input type="radio" name="review_type" value="manual" title="人工审核" {% if withdraw.review_type == "manual" %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提现平台</label>
            <div class="layui-input-block">
                <input type="checkbox" name="channels[]" value="alipay" title="支付宝" {% if 'alipay' in channels %}checked="checked"{% endif %}>
                <input type="checkbox" name="channels[]" value="wechat" title="微信" {% if 'wechat' in channels %}checked="checked"{% endif %}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提现次数</label>
            <div class="layui-input-block">
                <select name="monthly_limit" lay-verify="required">
                    {% for value in 1..10 %}
                        {% set selected = value == withdraw.monthly_limit ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }} 次 / 月</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">服务费比例</label>
            <div class="layui-input-block">
                <select name="service_rate" lay-verify="required">
                    {% for value in 0..30 %}
                        {% set selected = value == withdraw.service_rate ? 'selected="selected"' : '' %}
                        <option value="{{ value }}" {{ selected }}>{{ value }}%</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小金额（元）</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="min_amount" value="{{ withdraw.min_amount }}" lay-verify="number">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最大金额（元）</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="max_amount" value="{{ withdraw.max_amount }}" lay-verify="number">
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