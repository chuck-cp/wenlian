{% if exam_papers|length > 0 %}
    <table class="layui-table" lay-skin="line">
        <tr>
            <th>试卷</th>
            <th>题量</th>
            <th>时长</th>
            <th width="15%">操作</th>
        </tr>
        {% for paper in exam_papers %}
            {% set paper_url = url({'for':'home.exam_paper.show','id':paper.id}) %}
            <tr>
                <td>{{ paper.title }}</td>
                <td>{{ paper.question_count }}</td>
                <td>{{ paper.duration }}分钟</td>
                <td><a class="layui-btn layui-btn-sm" href="{{ paper_url }}" target="_blank">详情</a></td>
            </tr>
        {% endfor %}
    </table>
{% else %}
    <div class="no-records">没有相关记录</div>
{% endif %}
