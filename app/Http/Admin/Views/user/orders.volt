{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('order/macro') }}

    {% if pager.total_pages > 0 %}
        <table class="layui-table kg-table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col width="12%">
            </colgroup>
            <thead>
            <tr>
                <th>商品信息</th>
                <th>促销类型</th>
                <th>订单金额</th>
                <th>订单状态</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                {% set show_url = url({'for':'admin.order.show','id':item.id}) %}
                <tr>
                    <td>{{ item.subject }}（{{ item.id }}）</td>
                    <td>{{ promotion_type(item.promotion_type) }}</td>
                    <td>{{ '￥%0.2f'|format(item.amount) }}</td>
                    <td>{{ order_status(item.status) }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                    <td><a class="layui-btn layui-btn-sm" href="{{ show_url }}">查看详情</a></td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {{ partial('partials/pager_ajax') }}
    {% endif %}

{% endblock %}