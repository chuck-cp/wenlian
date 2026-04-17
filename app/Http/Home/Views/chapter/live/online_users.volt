{% if pager.total_items > 0 %}
    <div class="live-user-list">
        {% for item in pager.items %}
            {% set user_url = url({'for':'home.user.show','id':item.id}) %}
            <div class="live-user-card">
                <div class="avatar">
                    <img src="{{ item.avatar }}" alt="{{ item.name }}">
                </div>
                <div class="name layui-elip">
                    <a href="{{ user_url }}" target="_blank">{{ item.name }}</a>
                </div>
                <div class="action">
                    <span class="layui-btn layui-btn-sm layui-btn-danger btn-block" data-user-id="{{ item.id }}">禁言</span>
                </div>
            </div>
        {% endfor %}
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
