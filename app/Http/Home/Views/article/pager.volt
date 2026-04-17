{{ partial('macros/article') }}

{% if pager.total_pages > 0 %}
    <div class="article-list">
        <div class="layui-row layui-col-space20">
            {% for item in pager.items %}
                <div class="layui-col-md3">
                    {{ article_card(item) }}
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}