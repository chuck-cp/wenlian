{% if pager.total_items > 0 %}
    <div class="live-user-list">
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.id}) %}
            <div class="live-user-card" title="解禁时间：{{ date('Y-m-d H:i:s',item.expire_time) }}">
                <div class="avatar">
                    <img src="{{ item.user.avatar }}" alt="{{ item.user.name }}">
                </div>
                <div class="name layui-elip">
                    <a href="{{ user_url }}" target="_blank">{{ item.user.name }}</a>
                </div>
                <div class="action">
                    <span class="layui-btn layui-btn-sm layui-btn-danger btn-unblock" data-user-id="{{ item.user.id }}">解禁</span>
                </div>
            </div>
        {% endfor %}
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
