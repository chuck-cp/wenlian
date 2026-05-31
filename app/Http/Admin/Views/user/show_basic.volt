<table class="layui-table" lay-skin="line" lay-size="lg">
    <tr>
        <td>用户昵称：{{ user.name }}</td>
        {% if account.phone %}
            <td>手机号码：{{ account.phone }}</td>
        {% else %}
            <td>手机号码：N/A</td>
        {% endif %}
        {% if account.email %}
            <td>邮箱地址：{{ account.email }}</td>
        {% else %}
            <td>邮箱地址：N/A</td>
        {% endif %}
        {% if user.vip == 1 %}
            <td>会员期限：{{ date('Y-m-d H:i:s',user.vip_expiry_time) }}</td>
        {% else %}
            <td>会员期限：N/A</td>
        {% endif %}
    </tr>
    <tr>
        <td>注册时间：{{ date('Y-m-d H:i:s',user.create_time) }}</td>
        {% if user.active_time > 0 %}
            <td>活跃时间：{{ date('Y-m-d H:i:s',user.active_time) }}</td>
        {% else %}
            <td>活跃时间：N/A</td>
        {% endif %}
        {% if user.area %}
            <td>所在区域：{{ user.area }}</td>
        {% else %}
            <td>所在区域：N/A</td>
        {% endif %}
        <td>用户性别：{{ gender_info(user.gender) }}</td>
    </tr>
    <tr>
        <td>教学角色：{{ user.getEduRoleName() }}</td>
        {% if user.edu_role == 3 and user.edu_role_label %}
            <td>自定义名称：{{ user.edu_role_label }}</td>
        {% else %}
            <td></td>
        {% endif %}
        <td></td>
        <td></td>
    </tr>
    <!-- <tr>
        <td>可用积分：{{ balance.point }}</td>
        <td>可用资金：{{ '￥%0.2f'|format(balance.cash) }}</td>
        <td>可用发票：{{ '￥%0.2f'|format(balance.invoice) }}</td>
        <td></td>
    </tr> -->
</table>

<br>

<div class="kg-center">
    <button class="layui-btn layui-btn-primary kg-back">返回上页</button>
</div>