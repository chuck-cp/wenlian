{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.exam'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>考试配置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">切屏防护</label>
            <div class="layui-input-inline">
                <input type="radio" name="switch_anti_enabled" value="1" title="是" {% if exam.switch_anti_enabled == 1 %}checked="checked"{% endif %}>
                <input type="radio" name="switch_anti_enabled" value="0" title="否" {% if exam.switch_anti_enabled == 0 %}checked="checked"{% endif %}>
            </div>
            <div class="layui-form-mid">
                <span class="layui-font-gray">切换屏幕会遮盖试卷，试题模糊不可见，可以用来防止作弊。</span>
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
