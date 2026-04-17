{% block content %}

    {{ partial('macros/ownership') }}

    {% if pager.total_pages > 0 %}
        <table class="layui-table kg-table">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>课程名称</th>
                <th>来源类型</th>
                <th>学习进度</th>
                <th>累计时长</th>
                <th>创建时间</th>
                <th>过期时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                {% set learnings_url = url({'for':'admin.course.learnings','id':item.course_id},{'user_id':item.user_id}) %}
                {% set course_url = url({'for':'home.course.show','id':item.course.id}) %}
                {% set expiry_time = item.expiry_time > 0 ? date('Y-m-d H:i:s',item.expiry_time) : 'N/A' %}
                <tr>
                    <td><a href="{{ course_url }}" target="_blank">{{ item.course.title }}</a>（{{ item.course.id }}）</td>
                    <td>{{ join_source_type(item.source_type) }}</td>
                    <td> {{ item.progress }}%</td>
                    <td>{{ item.duration|duration }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                    <td>{{ expiry_time }}</td>
                    <td>
                        <button class="layui-btn layui-btn-sm kg-course-learnings" data-url="{{ learnings_url }}">学习记录</button>
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {{ partial('partials/pager_ajax') }}
    {% endif %}

{% endblock %}

{% block inline_js %}

    <script>

        layui.use(['jquery', 'layer'], function () {

            var $ = layui.jquery;
            var layer = layui.layer;

            $('.kg-course-learnings').on('click', function () {
                var url = $(this).data('url');
                layer.open({
                    type: 2,
                    title: '学习记录',
                    resize: false,
                    area: ['80%', '80%'],
                    content: [url]
                });
            });

        });

    </script>

{% endblock %}
