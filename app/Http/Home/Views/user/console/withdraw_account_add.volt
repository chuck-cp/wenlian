{% extends 'templates/main.volt' %}

{% block content %}

    {% set setting = setting('withdraw') %}
    {% set channels = setting['channels']|json_decode(true) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">提现 - 添加账户</span>
                </div>
                <form class="layui-form profile-form" method="post" action="{{ url({'for':'home.withdraw_account.create'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现平台</label>
                        <div class="layui-input-block">
                            <select name="channel" lay-filter="channel" lay-verify="required">
                                <option value="">请选择</option>
                                {% if 'alipay' in channels %}
                                    <option value="1">支付宝</option>
                                {% endif %}
                                {% if 'wechat' in channels %}
                                    <option value="2">微信</option>
                                {% endif %}
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item" id="channel-block" style="display:none;">
                        <label class="layui-form-label" id="channel-label">支付宝账号</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="account" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">真实姓名</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="name" placeholder="请填写与平台帐号相匹配的真实姓名" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                            <button class="layui-btn layui-btn-primary" type="reset">重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

{% endblock %}

{% block include_js %}

    {{ js_include('home/js/user.console.withdraw.account.js') }}

{% endblock %}