{% if pager.total_pages > 0 %}
    <table class="layui-table" lay-skin="line" lay-size="large">
        <colgroup>
            <col width="5%">
            <col width="15%">
            <col width="30%">
            <col width="40%">
            <col width="10%">
        </colgroup>
        {% for item in pager.items %}
            {% set order_url = url({'for':'home.groupon.order','id':item.groupon_id}) %}
            {% set owned_count = item.target_order_count - item.finish_order_count %}
            <tr>
                <td><img class="avatar-sm" src="{{ item.leader.avatar }}" alt="{{ item.leader.name }}"></td>
                <td>{{ item.leader.name }}</td>
                {% if item.status == 3 %}
                    <td align="center" class="green">已完成</td>
                {% else %}
                    <td align="center">还差 {{ owned_count }} 人</td>
                {% endif %}
                <td align="center">结束时间：{{ date('Y-m-d H:i:s',item.expire_time) }}</td>
                {% if item.me.allow_join == 1 %}
                    <td>
                        <button class="layui-btn layui-btn-danger btn-team-order" data-id="{{ item.id }}" data-url="{{ order_url }}">去拼单</button>
                    </td>
                {% else %}
                    <td>
                        <button class="layui-btn layui-btn-disabled">去拼单</button>
                    </td>
                {% endif %}
            </tr>
        {% endfor %}
    </table>
    {{ partial('partials/pager_ajax') }}
{% endif %}
