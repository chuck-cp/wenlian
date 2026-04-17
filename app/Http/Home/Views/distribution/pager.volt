{{ partial('macros/sale') }}

{%- macro sale_item_url(id,type) %}
    {% if type == 1 %}
        {% set url = url({'for':'home.course.show','id':id}) %}
    {% elseif type == 2 %}
        {% set url = url({'for':'home.package.show','id':id}) %}
    {% elseif type == 3 %}
        {% set url = url({'for':'home.vip.index'}) %}
    {% elseif type == 4 %}
        {% set url = url({'for':'home.exam_paper.show','id':id}) %}
    {% elseif type == 5 %}
        {% set url = url({'for':'home.article.show','id':id}) %}
    {% else %}
        {% set url = url({'for':'home.index'}) %}
    {% endif %}
    {% return url %}
{% endmacro %}

{% if pager.total_pages > 0 %}
    <div class="dist-list">
        <div class="layui-row layui-col-space20">
            {% for sale in pager.items %}
                {% set item_url = sale_item_url(sale.item.id,sale.item.type) %}
                {% set share_url = url({'for':'home.distribution.share','id':sale.id}) %}
                <div class="layui-col-md3">
                    <div class="course-card dist-card">
                        <div class="model">{{ sale_item_type_badge(sale.item.type) }}</div>
                        <div class="cover">
                            <a href="{{ item_url }}" target="_blank">
                                <img src="{{ sale.item.cover }}" alt="{{ sale.item.title }}" title="{{ sale.item.title }}">
                            </a>
                        </div>
                        <div class="info">
                            <div class="title layui-elip">
                                <a href="{{ item_url }}" title="{{ sale.item.title }}" target="_blank">{{ sale.item.title }}</a>
                            </div>
                            <div class="meta">
                                <span class="price">{{ '成功邀请奖励￥%0.2f'|format(sale.com_amount) }}</span>
                                <span class="share layui-badge" data-url="{{ share_url }}">立即分享</span>
                            </div>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
    {{ partial('partials/pager_ajax') }}
{% endif %}
