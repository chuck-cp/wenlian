{% if index_topics|length > 0 %}
    <div class="index-wrap wrap">
        <div class="header">专题课程</div>
        <div class="content simple">
            <div class="index-topic-list">
                <div class="layui-row layui-col-space20">
                    {% for topic in index_topics %}
                        <div class="layui-col-md3">
                            <div class="course-card">
                                <div class="cover">
                                    <a href="{{ topic.url }}" target="_blank">
                                        <img src="{{ topic.cover }}" alt="{{ topic.title }}" title="{{ topic.title }}">
                                    </a>
                                </div>
                                <div class="info">
                                    <div class="title layui-elip">
                                        <a href="{{ topic.url }}" title="{{ topic.title }}" target="_blank">{{ topic.title }}</a>
                                    </div>
                                    <div class="meta">
                                        <span>{{ topic.course_count }} 门课程</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
{% endif %}
