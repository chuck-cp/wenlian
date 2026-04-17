<div class="layui-card layui-text">
    <div class="layui-card-header">应用信息</div>
    <div class="layui-card-body">
        <table class="layui-table">
            <colgroup>
                <col width="25%">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <td>当前版本</td>
                <td>{{ app_info.alias }} {{ app_info.version }}</td>
            </tr>
            <tr>
                <td>授权主体</td>
                <td>{{ license_info.user_name }}</td>
            </tr>
            {% if auth_type == 'user_count' %}
                <tr>
                    <td>授权人数</td>
                    <td>{{ license_info.user_count }}</td>
                </tr>
            {% else %}
                <tr>
                    <td>过期时间</td>
                    <td>{{ date('Y-m-d H:i:s',license_info.expire_time) }}</td>
                </tr>
            {% endif %}
            </tbody>
        </table>
    </div>
</div>
