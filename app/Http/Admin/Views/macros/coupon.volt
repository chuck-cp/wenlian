{%- macro type_info(value) %}
    {% if value == 1 %}
        满减
    {% elseif value == 2 %}
        折扣
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro item_scope_info(id,type,info) %}
    {% if type == 0 %}
        所有商品
    {% elseif type == 1 %}
        {% if id == 0 %}
            所有课程
        {% else %}
            {% set url = url({'for':'home.course.show','id':info.id}) %}
            <a href="{{ url }}" title="{{ info.title }}" target="_blank">指定课程</a>
        {% endif %}
    {% elseif type == 2 %}
        {% if id == 0 %}
            所有套餐
        {% else %}
            {% set url = url({'for':'home.package.show','id':info.id}) %}
            <a href="{{ url }}" title="{{ info.title }}" target="_blank">指定套餐</a>
        {% endif %}
    {% elseif type == 3 %}
        {% if id == 0 %}
            所有会员
        {% else %}
            {% set url = url({'for':'home.vip.index'}) %}
            <a href="{{ url }}" title="{{ info.title }}" target="_blank">指定会员</a>
        {% endif %}
    {% elseif type == 4 %}
        {% if id == 0 %}
            所有试卷
        {% else %}
            {% set url = url({'for':'home.exam_paper.show','id':info.id}) %}
            <a href="{{ url }}" title="{{ info.title }}" target="_blank">指定试卷</a>
        {% endif %}
    {% elseif type == 5 %}
        {% if id == 0 %}
            所有专栏
        {% else %}
            {% set url = url({'for':'home.article.show','id':info.id}) %}
            <a href="{{ url }}" title="{{ info.title }}" target="_blank">指定专栏</a>
        {% endif %}
    {% endif %}
{%- endmacro %}

{%- macro attrs_info(type,attrs) %}
    {% if type == 1 %}
        <p>抵扣额度：{{ '￥%0.2f'|format(attrs.deduct_amount) }}</p>
    {% elseif type == 2 %}
        <p>抵扣比例：{{ attrs.discount_rate }}%</p>
        <p>最多抵扣：{{ '￥%0.2f'|format(attrs.max_deduct_amount) }}</p>
    {% endif %}
{%- endmacro %}
