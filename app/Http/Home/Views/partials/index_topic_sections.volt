{% if index_topic_sections|length > 0 %}
    {% for section in index_topic_sections %}
        <div class="index-wrap wrap index-topic-section">
            <div class="header index-topic-section-title">
                <a href="{{ section.url }}" target="_blank">{{ section.title }}</a>
            </div>
            <div class="content simple">
                <div class="index-course-list">
                    <div class="layui-row layui-col-space20">
                        {% for course in section.courses %}
                            <div class="layui-col-md3">
                                {{ course_card(course) }}
                            </div>
                        {% endfor %}
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}
{% endif %}
