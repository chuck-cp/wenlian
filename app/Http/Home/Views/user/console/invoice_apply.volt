{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/invoice_account') }}

    {% set invoice_setting = setting('invoice') %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">开票 - 申请发票</span>
                </div>
                <form class="layui-form" method="post" action="{{ url({'for':'home.invoice.create'}) }}">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">可用额度</label>
                            <div class="layui-form-mid red">{{ '￥%0.2f'|format(balance.invoice) }}</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发票抬头</label>
                        <div class="layui-input-inline" style="width:400px;">
                            <select name="account_id" lay-verify="required">
                                <option value="">请选择</option>
                                {% for item in accounts %}
                                    {% set head_type = head_type(item.head_type) %}
                                    {% set usage_type = usage_type(item.usage_type) %}
                                    {% set disabled = item.disabled == 1 ? 'disabled="disabled"' : '' %}
                                    <option value="{{ item.id }}" {{ disabled }}>{{ head_type }} - {{ item.head_name }} - {{ usage_type }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-form-mid">
                                <i class="layui-icon layui-icon-add-circle"></i>
                                <a href="{{ url({'for':'home.uc.invoice'},{'action':'account.add'}) }}">添加抬头</a>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">接收邮箱</label>
                        <div class="layui-input-block" style="width:400px;">
                            <input class="layui-input" type="text" name="post_email" placeholder="请填写接收发票的邮箱" lay-verify="email">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">开票金额</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="text" name="amount" lay-verify="number">
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
                    <ul class="my-invoice-tips">
                        <li>{{ '每个月最多开票 %s 次'|format(invoice_setting['monthly_limit']) }}</li>
                        <li>{{ '每次开票金额 %s - %s 元'|format(invoice_setting['min_amount'],invoice_setting['max_amount']) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

{% endblock %}