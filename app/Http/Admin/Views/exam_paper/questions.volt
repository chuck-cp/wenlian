{{ partial('macros/exam_question') }}

<div class="kg-pack-nav">
    <div class="layui-inline item">
        <button class="layui-btn layui-btn-sm layui-bg-blue kg-add-question">添加试题</button>
    </div>
    <div class="layui-inline item">
        <div class="layui-text">总计 {{ paper.total_score }} 分</div>
    </div>
</div>

{% for top in questions if top.question_count > 0 %}
    <p>{{ model_type(top.model) }}<span class="layui-text">（共{{ top.question_count }}题，计{{ top.total_score }}分）</span></p>
    <table class="layui-table layui-form kg-table" lay-skin="line">
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col width="50%">
            <col width="10%">
        </colgroup>
        <thead>
        <tr>
            <th>编号</th>
            <th>题型</th>
            <th>难度</th>
            <th>分值</th>
            <th>题干</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in top.questions %}
            <tr>
                <td>{{ item.id }}</td>
                <td>{{ model_type(item.model) }}</td>
                <td>{{ level_type(item.level) }}</td>
                <td>{{ item.score }}</td>
                <td>{{ item.topic }}</td>
                <td>
                    <button class="layui-btn layui-btn-sm layui-btn-danger kg-delete-question" data-paper-id="{{ paper.id }}" data-question-id="{{ item.id }}">删除</button>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    <br>
{% endfor %}