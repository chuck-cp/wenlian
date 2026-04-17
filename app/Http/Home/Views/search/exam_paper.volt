{{ partial('macros/exam_paper') }}

{% if pager.total_pages > 0 %}
    <div class="search-course-list">
        {% for item in pager.items %}
            {% set paper_url = url({'for':'home.exam_paper.show','id':item.id}) %}
            <div class="search-course-card">
                <div class="left">
                    <div class="model">
                        <span class="layui-badge layui-bg-green">{{ exam_type(item.exam_type) }}</span>
                    </div>
                    <div class="cover">
                        <a href="{{ paper_url }}" target="_blank">
                            <img src="{{ item.cover }}" alt="{{ item.title|striptags }}">
                        </a>
                    </div>
                </div>
                <div class="right">
                    <div class="title layui-elip">
                        <a href="{{ paper_url }}" target="_blank">{{ item.title }}</a>
                    </div>
                    <div class="summary">{{ item.summary }}</div>
                    <div class="meta">
                        <span>难度：{{ level_type(item.level) }}</span>
                        <span>学员：{{ item.join_count }}</span>
                        <span>收藏：{{ item.favorite_count }}</span>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% else %}
    {{ partial('search/empty') }}
{% endif %}
