{% if items|length > 0 %}
    <table class="layui-table" lay-skin="line">
    <colgroup>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
    <tr>
        <th>头像</th>
        <th>昵称</th>
        <th>加入时间</th>
    </tr>
    </thead>
    <tbody>
    {% for item in items %}
        {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
        <tr>
            <td><img class="avatar-sm" src="{{ item.user.avatar }}" alt="{{ item.user.name }}"></td>
            <td><a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a></td>
            <td>{{ date('Y-m-d H:i',item.create_time) }}</td>
        </tr>
    {% endfor %}
{% else %}
    <div class="no-records">没有相关记录</div>
{% endif %}