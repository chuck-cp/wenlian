{%- macro sale_status(value) %}
    {% if value == 1 %}
        未开始
    {% elseif value == 2 %}
        进行中
    {% elseif value == 3 %}
        已结束
    {% else %}
        N/A
    {% endif %}
{% endmacro %}
