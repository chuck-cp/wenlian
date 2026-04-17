{{ partial('macros/article') }}

{% if pager.total_pages > 0 %}
    <div class="article-list">
        <div class="layui-row layui-col-space20">
            {% for item in pager.items %}
                <div class="layui-col-md3">
                    {% set title = item.article.title %}
                    {% set url = url({'for':'home.article.show','id':item.article.id}) %}
                    <div class="course-card">
                        <div class="model">{{ source_type_badge(item.article.source_type) }}</div>
                        <div class="cover">
                            <a href="{{ url }}" title="{{ title }}" target="_blank">
                                <img src="{{ item.article.cover }}" alt="{{ title }}">
                            </a>
                        </div>
                        <div class="info">
                            <div class="title layui-elip">
                                <a href="{{ url }}" title="{{ title }}">{{ title }}</a>
                            </div>
                            <div class="meta">
                                <span>{{ item.article.user_count }} 学员</span>
                                <span>{{ item.article.view_count }} 浏览</span>
                            </div>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
