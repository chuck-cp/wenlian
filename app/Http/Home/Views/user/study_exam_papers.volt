{{ partial('macros/exam_paper') }}

{% if pager.total_pages > 0 %}
    <div class="course-list">
        <div class="layui-row layui-col-space20">
            {% for item in pager.items %}
                <div class="layui-col-md3">
                    {% set paper_title = item.exam_paper.title %}
                    {% set paper_url = url({'for':'home.exam_paper.show','id':item.exam_paper.id}) %}
                    <div class="course-card">
                        <div class="model">{{ exam_type_badge(item.exam_paper.exam_type) }}</div>
                        <div class="cover">
                            <a href="{{ paper_url }}" title="{{ paper_title }}" target="_blank">
                                <img src="{{ item.exam_paper.cover }}" alt="{{ paper_title }}">
                            </a>
                        </div>
                        <div class="info">
                            <div class="title layui-elip">
                                <a href="{{ paper_url }}" title="{{ paper_title }}">{{ paper_title }}</a>
                            </div>
                            <div class="meta">
                                <span>{{ item.exam_paper.join_count }} 学员</span>
                                <span>{{ item.exam_paper.favorite_count }} 收藏</span>
                            </div>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
