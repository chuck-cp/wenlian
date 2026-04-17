{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/invoice') }}
    {{ partial('macros/invoice_account') }}

    {% set status_types = {'0':'全部','1':'待处理','2':'已取消','3':'已审核','4':'已拒绝','5':'已完成'} %}
    {% set status = request.get('status','int','0') %}
    {% set account_list_url = url({'for':'home.uc.invoice'},{'action':'account.list'}) %}
    {% set apply_url = url({'for':'home.uc.invoice'},{'action':'apply'}) %}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">开票记录</span>
                    {% for key,value in status_types %}
                        {% set class = (status == key) ? 'layui-btn layui-btn-xs' : 'none' %}
                        {% set url = (key == '0') ? url({'for':'home.uc.invoice'},{'action':'list'}) : url({'for':'home.uc.invoice'},{'action':'list','status':key}) %}
                        <a class="{{ class }}" href="{{ url }}">{{ value }}</a>
                    {% endfor %}
                    <span class="sub-nav">
                        <a class="layui-btn layui-btn-sm" href="{{ apply_url }}">申请发票</a>
                        <a class="layui-btn layui-btn-sm layui-bg-blue" href="{{ account_list_url }}">发票抬头</a>
                    </span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-size="lg" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col width="20%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>开票信息</th>
                            <th>接收邮箱</th>
                            <th>创建时间</th>
                            <th>开票状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            {% set cancel_url = url({'for':'home.invoice.cancel','id':item.id}) %}
                            <tr>
                                <td>
                                    <p>抬头：<a href="javascript:" title="{{ account_summary_tips(item.account) }}">{{ item.account.head_name }}</a></p>
                                    <p>金额：{{ '￥%0.2f'|format(item.amount) }}</p>
                                </td>
                                <td>{{ item.post_email|default('N/A') }}</td>
                                <td>{{ date('Y-m-d',item.create_time) }}</td>
                                <td>{{ invoice_status(item.status) }}</td>
                                <td>
                                    {% if item.me.allow_cancel == 1 %}
                                        <button class="layui-btn layui-btn-sm layui-btn-danger kg-delete" data-tips="确定要取消吗？" data-url="{{ cancel_url }}">取消</button>
                                    {% else %}
                                        <button class="layui-btn layui-btn-sm layui-btn-disabled">取消</button>
                                    {% endif %}
                                    {% if item.voucher %}
                                        <a class="layui-btn layui-btn-sm layui-btn-normal" href="{{ item.voucher }}">下载</a>
                                    {% else %}
                                        <button class="layui-btn layui-btn-sm layui-btn-disabled">下载</button>
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