{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/cash_history') }}

    {% if pager.total_pages > 0 %}
        <table class="layui-table kg-table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>收支金额</th>
                <th>来源类型</th>
                <th>详细信息</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                <tr>
                    <td>{{ event_amount_info(item.event_amount) }}</td>
                    <td>{{ event_type_info(item.event_type) }}</td>
                    <td>{{ event_detail_info(item.event_type,item.event_info) }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {{ partial('partials/pager_ajax') }}
    {% endif %}

{% endblock %}