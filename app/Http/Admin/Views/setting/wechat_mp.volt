{% extends 'templates/main.volt' %}

{% block content %}

    <form class="layui-form kg-form" method="POST" action="{{ url({'for':'admin.setting.wechat_mp'}) }}">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>微信小程序</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">App ID</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="app_id" value="{{ mp.app_id }}" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">App Secret</label>
            <div class="layui-input-block">
                <input class="layui-input" type="text" name="app_secret" value="{{ mp.app_secret }}" lay-verify="required">
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