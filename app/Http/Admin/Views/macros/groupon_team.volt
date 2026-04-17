{%- macro team_status(value) %}
    {% if value == 1 %}
        待启动
    {% elseif value == 2 %}
        进行中
    {% elseif value == 3 %}
        已完成
    {% elseif value == 4 %}
        已关闭
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}
