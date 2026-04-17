{% block content %}

    {{ partial('macros/exam_paper') }}
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
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>试卷名称</th>
                <th>来源类型</th>
                <th>测评类型</th>
                <th>考试时长</th>
                <th>题目数量</th>
                <th>创建时间</th>
                <th>过期时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            {% for item in pager.items %}
                {% set learnings_url = url({'for':'admin.exam_paper.learnings','id':item.paper_id},{'user_id':item.user_id}) %}
                {% set exam_paper_url = url({'for':'home.exam_paper.show','id':item.exam_paper.id}) %}
                {% set expiry_time = item.expiry_time > 0 ? date('Y-m-d H:i:s',item.expiry_time) : 'N/A' %}
                <tr>
                    <td><a href="{{ exam_paper_url }}" target="_blank">{{ item.exam_paper.title }}</a>（{{ item.exam_paper.id }}）</td>
                    <td>{{ join_source_type(item.source_type) }}</td>
                    <td>{{ exam_type(item.exam_paper.exam_type) }}</td>
                    <td>{{ item.exam_paper.duration }}分钟</td>
                    <td>{{ item.exam_paper.question_count }}</td>
                    <td>{{ date('Y-m-d H:i:s',item.create_time) }}</td>
                    <td>{{ expiry_time }}</td>
                    <td>
                        <button class="layui-btn layui-btn-sm kg-paper-learnings" data-url="{{ learnings_url }}">考试记录</button>
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

            $('.kg-paper-learnings').on('click', function () {
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
