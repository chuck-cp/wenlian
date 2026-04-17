{%- macro model_type(value) %}
    {% if value == 1 %}
        单选题
    {% elseif value == 2 %}
        多选题
    {% elseif value == 3 %}
        判断题
    {% elseif value == 4 %}
        填空题
    {% elseif value == 5 %}
        简答题
    {% elseif value == 9 %}
        题帽题
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