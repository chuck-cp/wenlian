{%- macro exam_status(value) %}
    {% if value == 1 %}
        未开始
    {% elseif value == 2 %}
        进行中
    {% elseif value == 3 %}
        待批阅
    {% elseif value == 4 %}
        已完成
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}
