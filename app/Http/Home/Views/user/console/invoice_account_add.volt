{% extends 'templates/main.volt' %}

{% block content %}

    {% set setting = setting('invoice') %}
    {% set usage_types = setting['usage_types']|json_decode(true) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">开票 - 添加抬头</span>
                </div>
                <form class="layui-form label-110" method="post" action="{{ url({'for':'home.invoice_account.create'}) }}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">抬头类型</label>
                        <div class="layui-input-block">
                            <input type="radio" name="head_type" value="1" title="个人" lay-filter="head_type">
                            <input type="radio" name="head_type" value="2" title="企业" lay-filter="head_type">
                            <input type="radio" name="head_type" value="3" title="组织" lay-filter="head_type">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发票类型</label>
                        <div class="layui-input-block">
                            {% if 'normal' in usage_types %}
                                <input type="radio" name="usage_type" value="1" title="增值税普票" lay-filter="usage_type">
                            {% endif %}
                            {% if 'special' in usage_types %}
                                <input type="radio" name="usage_type" value="2" title="增值税专票" lay-filter="usage_type">
                            {% endif %}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发票抬头</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="head_name" placeholder="请输入发票抬头" lay-verify="required">
                        </div>
                    </div>
                    <div class="layui-form-item" id="tax-account-block">
                        <label class="layui-form-label">纳税人识别号</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" placeholder="请输入纳税人识别号" name="tax_account">
                        </div>
                    </div>
                    <div id="company-block" style="display:none;">
                        <div class="layui-form-item">
                            <label class="layui-form-label">基本开户银行</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" placeholder="请填写开户许可证上的开户银行" name="bank_name">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">基本开户账号</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" placeholder="请填写开户许可证上的银行账号" name="bank_account">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">企业注册地址</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" placeholder="请填写营业执照上的注册地址" name="company_address">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">企业注册电话</label>
                            <div class="layui-input-block">
                                <input class="layui-input" type="text" placeholder="请填写企业有效的联系电话" name="company_phone">
                            </div>
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

    {{ js_include('home/js/user.console.invoice.account.js') }}

{% endblock %}