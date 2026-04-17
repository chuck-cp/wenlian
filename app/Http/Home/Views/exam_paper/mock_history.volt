{{ partial('macros/exam_paper_user') }}

{% if pager.total_pages > 0 %}
    <table class="layui-table" lay-skin="line">
        <thead>
        <tr>
            <th>总分</th>
            <th>得分</th>
            <th>用时</th>
            <th>状态</th>
            <th>时间</th>
            <th width="15%">操作</th>
        </tr>
        </thead>
        {% for item in pager.items %}
            {% set explore_url = url({'for':'home.exam.mock_explore','id':item.id}) %}
            <tr>
                <td>{{ item.paper_score }}</td>
                <td>{{ item.status == 4 ? item.user_score : 'N/A' }}</td>
                <td>{{ item.user_duration|duration }}</td>
                <td>{{ exam_status(item.status) }}</td>
                <td>{{ item.start_time|time_ago }}</td>
                {% if item.status == 2 %}
                    <td><a class="layui-btn layui-btn-normal layui-btn-sm" href="{{ explore_url }}">继续考试</a></td>
                {% elseif item.status == 3 %}
                    {% if item.grade_type == 3 %}
                        <td><a class="layui-btn layui-btn-danger layui-btn-sm" href="{{ explore_url }}?manual_grade=1">阅卷评分</a></td>
                    {% elseif item.grade_type == 2 %}
                        <td><a class="layui-btn layui-btn-primary layui-btn-sm layui-btn-disabled" href="javascript:">查看详情</a></td>
                    {% endif %}
                {% elseif item.status == 4 %}
                    <td><a class="layui-btn layui-btn-primary layui-btn-sm" href="{{ explore_url }}">查看详情</a></td>
                {% endif %}
            </tr>
        {% endfor %}
    </table>
    {{ partial('partials/pager_ajax') }}
{% else %}
    <div class="no-records">没有相关记录</div>
{% endif %}
