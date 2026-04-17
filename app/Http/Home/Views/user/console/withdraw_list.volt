{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/withdraw') }}
    {{ partial('macros/withdraw_account') }}

    {% set status_types = {'0':'全部','1':'待处理','2':'已取消','3':'已审核','4':'已拒绝','5':'已完成'} %}
    {% set status = request.get('status','int','0') %}
    {% set account_list_url = url({'for':'home.uc.withdraw'},{'action':'account.list'}) %}
    {% set apply_url = url({'for':'home.uc.withdraw'},{'action':'apply'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">提现记录</span>
                    {% for key,value in status_types %}
                        {% set class = (status == key) ? 'layui-btn layui-btn-xs' : 'none' %}
                        {% set url = (key == '0') ? url({'for':'home.uc.withdraw'},{'action':'list'}) : url({'for':'home.uc.withdraw'},{'action':'list','status':key}) %}
                        <a class="{{ class }}" href="{{ url }}">{{ value }}</a>
                    {% endfor %}
                    <span class="sub-nav">
                        <a class="layui-btn layui-btn-sm" href="{{ apply_url }}">申请提现</a>
                        <a class="layui-btn layui-btn-sm layui-bg-blue" href="{{ account_list_url }}">提现账户</a>
                    </span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-size="lg" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col width="15%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>提现金额</th>
                            <th>提现帐号</th>
                            <th>申请时间</th>
                            <th>提现状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set cancel_url = url({'for':'home.withdraw.cancel','id':item.id}) %}
                            <tr>
                                <td><a href="javascript:" title="{{ '服务费：￥%0.2f'|format(item.service_fee) }}" class="red">{{ '￥%0.2f'|format(item.apply_amount) }}</a></td>
                                <td>{{ item.account.account }}（{{ channel_type(item.account.channel) }}）</td>
                                <td>{{ date('Y-m-d H:i',item.create_time) }}</td>
                                <td>{{ withdraw_status(item.status) }}</td>
                                <td>
                                    {% if item.me.allow_cancel == 1 %}
                                        <button class="layui-btn layui-btn-sm layui-btn-danger kg-delete" data-tips="确定要取消吗？" data-url="{{ cancel_url }}">取消</button>
                                    {% else %}
                                        <button class="layui-btn layui-btn-sm layui-btn-disabled">取消</button>
                                    {% endif %}
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    {{ partial('partials/pager') }}
                {% endif %}
            </div>
        </div>
    </div>

{% endblock %}