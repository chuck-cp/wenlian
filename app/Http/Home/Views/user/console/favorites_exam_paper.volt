{% if pager.total_pages > 0 %}
    <table class="layui-table" lay-size="lg" lay-skin="line">
        <colgroup>
            <col>
            <col>
            <col>
            <col width="12%">
        </colgroup>
        <thead>
        <tr>
            <th>考试</th>
            <th>学员</th>
            <th>收藏</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {% for item in pager.items %}
            {% set exam_paper_url = url({'for':'home.exam_paper.show','id':item.id}) %}
            {% set favorite_url = url({'for':'home.course.favorite','id':item.id}) %}
            <tr>
                <td><a href="{{ exam_paper_url }}" target="_blank">{{ item.title }}</a></td>
                <td>{{ item.join_count }}</td>
                <td>{{ item.favorite_count }}</td>
                <td class="center">
                    <button class="layui-btn layui-btn-sm layui-btn-danger kg-delete" data-tips="确定要取消收藏吗？" data-url="{{ favorite_url }}">取消</button>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    {{ partial('partials/pager') }}
{% endif %}