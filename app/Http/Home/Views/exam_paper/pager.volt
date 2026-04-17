{{ partial('macros/exam_paper') }}

{% if pager.total_pages > 0 %}
    <div class="exam-paper-list">
        <div class="layui-row layui-col-space20">
            {% for item in pager.items %}
                <div class="layui-col-md3">
                    {{ exam_paper_card(item) }}
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
