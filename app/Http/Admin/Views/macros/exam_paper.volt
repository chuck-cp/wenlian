{%- macro exam_type(value) %}
    {% if value == 1 %}
        模拟考试
    {% elseif value == 2 %}
        同步练习
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro pack_type(value) %}
    {% if value == 1 %}
        人工组卷
    {% elseif value == 2 %}
        随机组卷
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro level_type(value) %}
    {% if value == 1 %}
        初级
    {% elseif value == 2 %}
        中级
    {% elseif value == 3 %}
        高级
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro tags_info(items) %}
    {% for item in items %}
        {% set comma = loop.last ? '' : ',' %}
        {{ item.name ~ comma }}
    {% endfor %}
{%- endmacro %}