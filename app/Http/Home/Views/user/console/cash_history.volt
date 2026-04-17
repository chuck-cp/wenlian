{% extends 'templates/main.volt' %}

{% block content %}

    {{ partial('macros/cash_history') }}

    <div class="layout-main">
        <div class="my-sidebar">{{ partial('user/console/menu') }}</div>
        <div class="my-content">
            <div class="wrap">
                <div class="my-nav">
                    <span class="title">收支记录</span>
                </div>
                {% if pager.total_pages > 0 %}
                    <table class="layui-table" lay-skin="line">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>金额</th>
                            <th>来源</th>
                            <th>详情</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in pager.items %}
                            <tr>
                                <td>{{ event_amount_info(item.event_amount) }}</td>
                                <td>{{ event_type_info(item.event_type) }}</td>
                                <td>{{ event_detail_info(item.event_type,item.event_info) }}</td>
                                <td>{{ date('Y-m-d H:i',item.create_time) }}</td>
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