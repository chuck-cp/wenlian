{% if items|length > 0 %}
    {% for item in items %}
        {% set user_url = url({'for':'home.user.show','id':item.user.id}) %}
        <div class="rank-user-card">
            <div class="sort">
                {% if loop.index == 1 %}
                    <span class="layui-badge layui-bg-red">{{ loop.index }}</span>
                {% elseif loop.index == 2 %}
                    <span class="layui-badge layui-bg-blue">{{ loop.index }}</span>
                {% elseif loop.index == 3 %}
                    <span class="layui-badge layui-bg-orange">{{ loop.index }}</span>
                {% else %}
                    <span class="layui-badge layui-bg-gray">{{ loop.index }}</span>
                {% endif %}
            </div>
            <div class="avatar">
                <img src="{{ item.user.avatar }}" alt="{{ item.user.name }}">
            </div>
            <div class="info">
                <div class="name layui-elip">
                    <a href="{{ user_url }}" title="{{ item.user.about }}" target="_blank">{{ item.user.name }}</a>
                </div>
                <div class="title layui-elip">{{ item.user.title|default('小小书童') }}</div>
            </div>
            <div class="score">{{ item.user_score }}分</div>
        </div>
    {% endfor %}
{% else %}
    <div class="no-records">没有相关记录</div>
{% endif %}