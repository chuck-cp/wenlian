{%- macro pay_status(value) %}
    {% if value == 1 %}
        待支付
    {% elseif value == 2 %}
        已支付
    {% elseif value == 3 %}
        已关闭
    {% else %}
        N/A
    {% endif %}
{%- endmacro %}
