{%- macro item_full_info(item_type,item_info) %}
    {% if item_type == 1 %}
        {% set course = item_info.course %}
        <p>名称：{{ course.title }}（{{ course.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(course.price) }}</p>
    {% elseif item_type == 2 %}
        {% set package = item_info.package %}
        <p>名称：{{ package.title }}（{{ package.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(package.price) }}</p>
    {% elseif item_type == 3 %}
        {% set vip = item_info.vip %}
        <p>名称：{{ vip.title }}（{{ vip.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(vip.price) }}</p>
    {% elseif item_type == 4 %}
        {% set exam_paper = item_info.exam_paper %}
        <p>名称：{{ exam_paper.title }}（{{ exam_paper.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(exam_paper.price) }}</p>
    {% elseif item_type == 5 %}
        {% set article = item_info.article %}
        <p>名称：{{ article.title }}（{{ article.id }}）</p>
        <p>类型：{{ sale_item_type(item_type) }}　价格：{{ '￥%0.2f'|format(article.price) }}</p>
    {% endif %}
{%- endmacro %}

{%- macro schedules_info(schedules) %}
    {% for value in schedules %}
        <span class="layui-badge layui-bg-gray">{{ value }}点</span>
    {% endfor %}
{%- endmacro %}
