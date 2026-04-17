{{ partial('macros/exam_paper') }}

{% if exam_papers|length > 0 %}
    <div class="layui-card">
        <div class="layui-card-header">推荐试卷</div>
        <div class="layui-card-body">
            {% for paper in exam_papers %}
                {{ sidebar_exam_paper_card(paper) }}
            {% endfor %}
        </div>
    </div>
{% endif %}