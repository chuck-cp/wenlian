{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/withdraw_account') }}

    {% set withdraw_setting = setting('withdraw') %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">提现 - 申请提现</span>
                </div>
                <form class="layui-form" method="post" action="{{ url({'for':'home.withdraw.create'}) }}">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">可用金额</label>
                            <div class="layui-form-mid red">{{ '￥%0.2f'|format(balance.cash) }}</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现账户</label>
                        <div class="layui-input-inline" style="width:400px;">
                            <select name="account_id" lay-verify="required">
                                <option value="">请选择</option>
                                {% for item in accounts %}
                                    {% set channel = channel_type(item.channel) %}
                                    {% set disabled = item.disabled == 1 ? 'disabled="disabled"' : '' %}
                                    <option value="{{ item.id }}" {{ disabled }}>{{ item.name }} - {{ item.account }} - {{ channel|trim }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-form-mid">
                                <i class="layui-icon layui-icon-add-circle"></i>
                                <a href="{{ url({'for':'home.uc.withdraw'},{'action':'account.add'}) }}">添加账户</a>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现金额</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="text" name="apply_amount" lay-verify="number">
                        </div>
                        <div class="layui-form-mid gray">元</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">登录密码</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="password" name="login_password" autocomplete="off" lay-verify="required">
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
            <div class="layui-card">
                <div class="layui-card-header">温馨提示</div>
                <div class="layui-card-body">
                    <ul class="my-withdraw-tips">
                        <li>{{ '每个月最多提现 %s 次'|format(withdraw_setting['monthly_limit']) }}</li>
                        <li>{{ '每次提现金额 %s - %s 元'|format(withdraw_setting['min_amount'],withdraw_setting['max_amount']) }}</li>
                        <li>{{ '提现服务费 %s'|format(withdraw_setting['service_rate']) }}%</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

{% endblock %}