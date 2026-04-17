{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('withdraw/macro') }}

    {% set withdraw_sh_url = url({'for':'admin.withdraw.status_history','id':withdraw.id}) %}
    {% set withdraw_review_url = url({'for':'admin.withdraw.review','id':withdraw.id}) %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>提现信息</legend>
    </fieldset>

    <table class="layui-table kg-table">
        <tr>
            <th>提现编号</th>
            <th>帐目金额</th>
            <th>提现备注</th>
            <th>提现状态</th>
            <th>创建时间</th>
        </tr>
        <tr>
            <td>{{ withdraw.sn }}</td>
            <td>
                <p>申请金额：{{ '￥%0.2f'|format(withdraw.apply_amount) }}</p>
                <p>转账金额：{{ '￥%0.2f'|format(withdraw.trans_amount) }}</p>
                <p>服务费用：{{ '￥%0.2f'|format(withdraw.service_fee) }}</p>
            </td>
            <td>
                {% if withdraw.apply_note %}
                    <p class="layui-elip" title="{{ withdraw.apply_note }}">申请备注：{{ withdraw.apply_note }}</p>
                {% endif %}
                {% if withdraw.review_note %}
                    <p class="layui-elip" title="{{ withdraw.review_note }}">审核意见：{{ withdraw.review_note }}</p>
                {% endif %}
            </td>
            <td><a class="kg-status-history" href="javascript:" title="查看历史状态" data-url="{{ withdraw_sh_url }}">{{ withdraw_status(withdraw.status) }}</a></td>
            <td>{{ date('Y-m-d H:i:s',withdraw.create_time) }}</td>
        </tr>
    </table>

    <br>

    {% if withdraw.status == 1 %}
        <form class="layui-form kg-form" method="POST" action="{{ withdraw_review_url }}">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>提现审核</legend>
            </fieldset>
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
    {% else %}
        <div class="kg-center">
            <button class="layui-btn layui-btn-primary kg-back">返回上页</button>
        </div>
    {% endif %}

    <fieldset class="layui-elem-field layui-field-title">
        <legend>提现账户</legend>
    </fieldset>

    <table class="layui-table kg-table">
        <tr>
            <th>真实姓名</th>
            <th>提现账号</th>
            <th>提现平台</th>
            <th>创建时间</th>
        </tr>
        <tr>
            <td>{{ withdraw_account.name }}</td>
            <td>{{ withdraw_account.account }}</td>
            <td>{{ withdraw_channel(withdraw_account.channel) }}</td>
            <td>{{ date('Y-m-d H:i:s',withdraw_account.create_time) }}</td>
        </tr>
    </table>

{% endblock %}

{% block include_js %}

    {{ js_include('admin/js/status-history.js') }}

{% endblock %}