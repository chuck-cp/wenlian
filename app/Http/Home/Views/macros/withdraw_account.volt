{%- macro channel_type(value) %}
    {% if value == 1 %}
        {% return '支付宝' %}
    {% elseif value == 2 %}
        {% return '微信' %}
    {% else %}
        {% return 'N/A' %}
    {% endif %}
{%- endmacro %}