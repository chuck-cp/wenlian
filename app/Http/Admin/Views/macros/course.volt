{%- macro model_type(value) %}
    {% if value == 1 %}
        点播
    {% elseif value == 2 %}
        直播
    {% elseif value == 3 %}
        图文
    {% elseif value == 4 %}
        面授
    {% elseif value == 5 %}
        文档
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}

{%- macro level_type(value) %}
    {% if value == 1 %}
        入门
    {% elseif value == 2 %}
        初级
    {% elseif value == 3 %}
        中级
    {% elseif value == 4 %}
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