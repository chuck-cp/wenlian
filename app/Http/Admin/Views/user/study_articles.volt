{% block content %}

    {{ partial('macros/ownership') }}

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
                <th>文章名称</th>
                <th>来源类型</th>
                <th>创建时间</th>
                <th>过期时间</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                {% set article_url = url({'for':'home.article.show','id':item.article.id}) %}
                {% set expiry_time = item.expiry_time > 0 ? date('Y-m-d H:i:s',item.expiry_time) : 'N/A' %}
                <tr>
                    <td><a href="{{ article_url }}" target="_blank">{{ item.article.title }}</a>（{{ item.article.id }}）</td>
                    <td>{{ join_source_type(item.source_type) }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                    <td>{{ expiry_time }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {{ partial('partials/pager_ajax') }}
    {% endif %}

{% endblock %}
