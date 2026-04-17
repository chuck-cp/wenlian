{%- macro status_info(start_time,end_time) %}
    {% if start_time > time() %}
        未开始
    {% elseif end_time > time() %}
        进行中
    {% elseif end_time < time() %}
        已结束
    {% endif %}
{%- endmacro %}

{%- macro item_full_info(item_type,item_info) %}
    {% if item_type == 1 %}
        {% set course = item_info.course %}
        {% set url = url({'for':'home.course.show','id':course.id}) %}
        <p>名称：<a href="{{ url }}" target="_blank">{{ course.title }}</a>（{{ course.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(course.price) }}</p>
    {% elseif item_type == 2 %}
        {% set package = item_info.package %}
        {% set url = url({'for':'home.package.show','id':package.id}) %}
        <p>名称：<a href="{{ url }}" target="_blank">{{ package.title }}</a>（{{ package.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(package.price) }}</p>
    {% elseif item_type == 3 %}
        {% set vip = item_info.vip %}
        {% set url = url({'for':'home.vip.index'}) %}
        <p>名称：<a href="{{ url }}">{{ vip.title }}</a>（{{ vip.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(vip.price) }}</p>
    {% elseif item_type == 4 %}
        {% set exam_paper = item_info.exam_paper %}
        {% set url = url({'for':'home.exam_paper.show','id':exam_paper.id}) %}
        <p>名称：<a href="{{ url }}" target="_blank">{{ exam_paper.title }}</a>（{{ exam_paper.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(exam_paper.price) }}</p>
    {% elseif item_type == 5 %}
        {% set article = item_info.article %}
        {% set url = url({'for':'home.article.show','id':article.id}) %}
        <p>名称：<a href="{{ url }}" target="_blank">{{ article.title }}</a>（{{ article.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(article.price) }}</p>
    {% endif %}
{%- endmacro %}
