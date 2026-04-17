{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('invoice/macro') }}

    {% set status_history_url = url({'for':'admin.invoice.status_history','id':invoice.id}) %}
    {% set review_url = url({'for':'admin.invoice.review','id':invoice.id}) %}
    {% set voucher_url = url({'for':'admin.invoice.voucher','id':invoice.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>开票信息</legend>
    </fieldset>

    <table class="layui-table kg-table">
        <tr>
            <th>发票类型</th>
            <th>发票金额</th>
            <th>接收邮箱</th>
            <th>开票备注</th>
            <th>开票状态</th>
            <th>创建时间</th>
        </tr>
        <tr>
            <td>{{ usage_type(invoice_account.usage_type) }}</td>
            <td>{{ '￥%0.2f'|format(invoice.amount) }}</td>
            <td>{{ invoice.post_email|default('N/A') }}</td>
            <td>
                {% if invoice.apply_note %}
                    <p class="layui-elip" title="{{ invoice.apply_note }}">申请备注：{{ invoice.apply_note }}</p>
                {% endif %}
                {% if invoice.review_note %}
                    <p class="layui-elip" title="{{ invoice.review_note }}">审核意见：{{ invoice.review_note }}</p>
                {% endif %}
            </td>
            <td><a class="kg-status-history" href="javascript:" title="查看历史状态" data-url="{{ status_history_url }}">{{ invoice_status(invoice.status) }}</a></td>
            <td>{{ date('Y-m-d H:i:s',invoice.create_time) }}</td>
        </tr>
    </table>

    <br>

    <fieldset class="layui-elem-field layui-field-title">
        <legend>买方信息</legend>
    </fieldset>

    <table class="layui-table kg-table">
        <tr>
            <th>类型</th>
            <th>名称</th>
            <th>纳税人识别号</th>
            <th>银行账号</th>
            <th>公司信息</th>
        </tr>
        <tr>
            <td>{{ head_type(invoice_account.head_type) }}</td>
            <td>{{ invoice_account.head_name }}</td>
            <td>{{ invoice_account.tax_account|default('N/A') }}</td>
            <td>
                {% if invoice_account.bank_account %}
                    <p>开户银行：{{ invoice_account.bank_name }}</p>
                    <p>开户帐号：{{ invoice_account.bank_account }}</p>
                {% else %}
                    <p>N/A</p>
                {% endif %}
            </td>
            <td>
                {% if invoice_account.company_address %}
                    <p>注册地址：{{ invoice_account.company_address }}</p>
                    <p>注册电话：{{ invoice_account.company_phone }}</p>
                {% else %}
                    <p>N/A</p>
                {% endif %}
            </td>
        </tr>
    </table>

    <br>

    {% if invoice.status == 1 %}
        <fieldset class="layui-elem-field layui-field-title">
            <legend>开票审核</legend>
        </fieldset>
        <form class="layui-form kg-form" method="POST" action="{{ review_url }}">
            <div class="layui-form-item">
                <label class="layui-form-label">审核结果</label>
                <div class="layui-input-block">
                    <input type="radio" name="review_status" value="3" title="同意" checked="checked">
                    <input type="radio" name="review_status" value="4" title="拒绝">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">审核说明</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="review_note" lay-verify="required">
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
    {% endif %}

    <div class="kg-center">
        <button class="layui-btn layui-btn-primary kg-back">返回上页</button>
    </div>

    <br>

    {% if invoice.status in [3,5] %}
        <fieldset class="layui-elem-field layui-field-title">
            <legend>发票信息</legend>
        </fieldset>
        <form class="layui-form kg-form" method="POST" action="{{ voucher_url }}">
            <div class="layui-form-item">
                <label class="layui-form-label">发票号码</label>
                <div class="layui-input-block">
                    <input class="layui-input" type="text" name="full_no" value="{{ invoice.sort_no ~ invoice.serial_no }}" placeholder="20位数字（前12位为发票代码，后8位为发票序号）" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">发票凭证</label>
                <div class="layui-input-inline" style="width:500px;">
                    <input id="tc-voucher" class="layui-input" type="text" name="voucher" value="{{ invoice.voucher }}" placeholder="请上传pdf或ofd格式发票文件" lay-verify="required">
                </div>
                <div class="layui-input-inline">
                    <button id="change-voucher" class="layui-btn" type="button" style="margin-right:10px;">上传</button>
                    {% if invoice.voucher %}
                        <button class="kg-copy layui-btn layui-bg-blue" type="button" data-clipboard-target="#tc-voucher">复制</button>
                    {% endif %}
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="true" lay-filter="go">提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    {% endif %}

{% endblock %}

{% block include_js %}

    {{ js_include('admin/js/status-history.js') }}
    {{ js_include('admin/js/invoice.upload.js') }}
    {{ js_include('lib/clipboard.min.js') }}
    {{ js_include('admin/js/copy.js') }}

{% endblock %}